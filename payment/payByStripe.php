<!-- class StripeController extends AbstractController
{
    #[Route('/buy/{id}', name: 'course_buy')]
    public function buy(
        Course $course,
        Request $request,
        StripeClient $stripe,
        ParameterBagInterface $params,
        EntityManagerInterface $em
    ): Response {
        
        // je récupère le user connecté
        $user = $this->getUser();

        // je récupère le taux de tva que j'ai créé au préalable sur Stripe
        $taxRateId = $params->get('stripe.tax_rate_id');

        // Crée un prix Stripe si le cours n'en a pas encore
        if (!$course->getStripePriceId()) {

            // je créé le produit dans stripe si il existe pas
            // si il n'a jamais été acheté
            $product = $stripe->products->create([
                'name' => $course->getTitle(),
            ]);

            $priceAmount = (float) str_replace(',', '.', $course->getPrice());

            // ça ça me renvoit la référence du produit créé stocké dans stripe
            $stripePrice = $stripe->prices->create([
                'unit_amount' => intval($priceAmount * 100),
                'currency' => 'eur',
                'product' => $product->id,
                'tax_behavior' => 'exclusive', // Important !
            ]);

            // Stocke le price_id dans la BDD
            $course->setStripePriceId($stripePrice->id);
            $em->persist($course);
            $em->flush();
        }

        // On génère l'URL de succès avec le placeholder Stripe
        $successUrl = $this->getParameter('app.base_url') . '/payment/success/' . $course->getId() . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $session = $stripe->checkout->sessions->create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $course->getStripePriceId(),
                    'quantity' => 1,
                    'tax_rates' => [$taxRateId],
                ]
            ],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_creation' => 'always'
        ]);

        return $this->redirect($session->url);
    }

    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function paymentSuccess(
        int $id,
        Security $security,
        MailerInterface $mailer,
        EntityManagerInterface $em,
        Pdf $pdfGenerator,
        CourseRepository $courseRepository,
        Request $request
    ): Response {

        $user = $security->getUser();

        // session stripe lié au paiement actuel
        $sessionId = $request->query->get('session_id');

        // ✅ Vérifie si le paiement a déjà été enregistré et validé
        $existingPayment = $em->getRepository(Payment::class)->findOneBy([
            'stripeSessionId' => $sessionId,
            'user' => $user
        ]);

        // si le gars est déjà tombé dans la page de confirmation
        // je le redirige vers la page liée aux factures
        // car je veux pas enregistrer son paiement une deuxième fois
        if ($existingPayment && $existingPayment->isVerified()) {
            // 🔁 Redirige vers les factures si déjà traité
            $this->addFlash('info', 'Ce paiement a déjà été traité.');
            return $this->redirectToRoute('app_profile_invoices');
        }

        $course = $em->getRepository(Course::class)->find($id);
        // Nouveau paiement
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setCourse($course);
        $priceAmount = (float) str_replace(',', '.', $course->getPrice());
        $payment->setAmount($priceAmount);
        $payment->setPaidAt(new \DateTimeImmutable());
        $payment->setStripeSessionId($sessionId);
        $payment->setType('course');

        $em->persist($payment);
        $em->flush();


        // 1. Récupération du dernier paiement
        $payment = $em->getRepository(Payment::class)->findOneBy(
            ['user' => $user],
            ['paidAt' => 'DESC']
        );

        if (!$payment) {
            throw $this->createNotFoundException("Aucun paiement trouvé");
        }

        $course = $payment->getCourse();

        // 2. Vérifie si l'utilisateur a déjà accès au cours
        $existingAccess = $em->getRepository(UserCourse::class)->findOneBy([
            'user' => $user,
            'course' => $course
        ]);

        if (!$existingAccess) {
            // Création de la relation UserCourse
            $userCourse = new UserCourse();
            $userCourse->setUser($user);
            $userCourse->setCourse($course);
            $userCourse->setAccessGrantedAt(new \DateTime());
            $em->persist($userCourse);
        }

        // 3. Génération de la facture PDF
        $pdfContent = $pdfGenerator->getOutputFromHtml(
            $this->renderView('pdf/invoice.html.twig', [
                'user' => $payment->getUser(),
                'course' => $payment->getCourse(),
                'payment' => $payment
            ])
        );

        $invoiceDir = $this->getParameter('kernel.project_dir') . '/public/invoices';

        // ✅ Crée le dossier s'il n'existe pas
        if (!is_dir($invoiceDir)) {
            mkdir($invoiceDir, 0775, true);
        }

        // Je créer la facture PDF
        $invoiceFilename = 'facture-' . $payment->getId() . '.pdf';
        $invoicePath = $invoiceDir . '/' . $invoiceFilename;
        file_put_contents($invoicePath, $pdfContent);

        // je lie le paiement à cette facture
        $payment->setInvoiceFilename($invoiceFilename);
        $payment->setIsVerified(true);

        $em->persist($payment);
        $em->flush();

        // 4. Envoi de l’email avec la facture en pièce jointe
        $email = (new TemplatedEmail())
            ->from('hello@tonsite.com')
            ->to($user->getEmail())
            ->subject('Confirmation de votre achat')
            ->htmlTemplate('emails/payment_success.html.twig')
            ->context([
                'user' => $payment->getUser(),
                'course' => $payment->getCourse(),
                'payment' => $payment,
            ])
            ->attachFromPath($invoicePath, $invoiceFilename);

        $mailer->send($email);

        // 6. Sauvegarde en base
        $em->flush();

        // 7. Récupération de toutes les factures
        $payments = $em->getRepository(Payment::class)->findBy(['user' => $user], ['paidAt' => 'DESC']);

        return $this->render('invoices/invoice.html.twig', [
            'payment' => $payment,
            'payments' => $payments
        ]);

    }


    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('warning', 'Le paiement a été annulé.');
        return $this->redirectToRoute('course_index'); // ou autre page pertinente
    }
    
    }
    
    
    
    
    
 

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Course $course = null;

    #[ORM\Column(length: 255)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripePaymentIntentId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cardBrand = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $cardLast4 = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isSuccessful = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = 'eur';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceFilename = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 20)]
    private string $type = 'course'; // ou 'subscription'

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
        return $this;
    }

    public function getCardBrand(): ?string
    {
        return $this->cardBrand;
    }

    public function setCardBrand(?string $cardBrand): self
    {
        $this->cardBrand = $cardBrand;
        return $this;
    }

    public function getCardLast4(): ?string
    {
        return $this->cardLast4;
    }

    public function setCardLast4(?string $cardLast4): self
    {
        $this->cardLast4 = $cardLast4;
        return $this;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function setIsSuccessful(bool $isSuccessful): self
    {
        $this->isSuccessful = $isSuccessful;
        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getInvoiceFilename(): ?string
    {
        return $this->invoiceFilename;
    }

    public function setInvoiceFilename(?string $invoiceFilename): self
    {
        $this->invoiceFilename = $invoiceFilename;
        return $this;
    }

    public function __toString(): string
    {
        return ""; // Retourne le name de l'utilisateur comme chaîne, nécessaire dans CourseCrudController
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

        
} -->
