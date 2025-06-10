<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\EventDetail;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\EmailService;
use App\Service\PdfGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class VenteController extends AbstractController
{
    #[Route('/', name: 'app_vente')]
    public function index(ProductRepository $product): Response
    {
        $articles = $product->findAll();

        return $this->render('vente/index.html.twig', [
            'controller_name' => 'VenteController',
            'articles' => $articles,
        ]);
    }

    #[Route('/vente/cart/add', name: 'vente_cart_add', methods: ['POST'])]
    public function add(Request $request, ProductRepository $productRepo, SessionInterface $session): JsonResponse
    {
        try {
            $articleId = $request->request->get('id');
            $quantity = max(1, (int) $request->request->get('quantity', 1));
            $article = $productRepo->find($articleId);

            if (!$article) {
                return new JsonResponse(['error' => 'Article introuvable'], 404);
            }

            $cart = $session->get('cart', []);
            $currentQty = $cart[$articleId]['quantity'] ?? 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $article->getStock()) {
                return new JsonResponse(['error' => 'Stock insuffisant'], 400);
            }

            $cart[$articleId] = [
                'name' => $article->getName(),
                'price' => $article->getPrice(),
                'quantity' => $newQty,
            ];

            $session->set('cart', $cart);

            return new JsonResponse([
                'success' => true,
                'cart' => $cart,
                'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/vente/cart/content', name: 'vente_cart_content', methods: ['GET'])]
    public function content(SessionInterface $session): JsonResponse
    {
       
        $cart = $session->get('cart', []);
        return new JsonResponse([
            'cart' => $cart,
            'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
        ]);
    }

    #[Route('/vente/cart/remove', name: 'cart_remove', methods: ['POST'])]
    public function remove(Request $request, SessionInterface $session): JsonResponse
    {
        try {
            $id = $request->request->get('id');
            $cart = $session->get('cart', []);

            if (isset($cart[$id])) {
                unset($cart[$id]);
                $session->set('cart', $cart);
            }

            return new JsonResponse([
                'cart' => $cart,
                'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Erreur de suppression du panier'], 500);
        }
    }

    #[Route('/vente/checkout', name: 'order_checkout', methods: ['POST', 'GET'])]
    public function checkout(SessionInterface $session): Response
    {
      
        if (!$this->getUser()) {
            return $this->redirectToRoute('cart_show_not_logged');
        }

        $cart = $session->get('cart', []);
        return $this->render('cart/checkout.html.twig', [
            'cart' => $cart,
            'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
        ]);
    }

    #[Route('/vente/cart/not-logged', name: 'cart_show_not_logged')]
    public function notLogged(): Response
    {
        $this->addFlash('error', 'Vous devez être connecté pour valider une commande.');
        return $this->render('cart/not_logged.html.twig');
    }

#[Route('/vente/checkout/confirm', name: 'vente_order_confirm', methods: ['POST'])]
public function confirmOrder(
    Request $request,
    SessionInterface $session,
    EntityManagerInterface $em,
    PdfGeneratorService $pdfGeneratorService,
    EmailService $emailService
): Response {
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('cart_show_not_logged');
    }

    $cart = $session->get('cart', []);
    if (empty($cart)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('order_checkout');
    }

    try {
        // 1. Récupération des données du formulaire
        $modeRetrait = $request->request->get('modeRetrait');
        $dateRetrait = new \DateTime($request->request->get('dateRetrait'));
        $dateRetour = new \DateTime($request->request->get('dateRetour'));
        $commentaire = $request->request->get('commentaire');

        // 2. Création de la commande
        $commande = new Commande();
        $commande->setUser($user);
        $commande->setDateRetrait($dateRetrait);
        $commande->setDateRetour($dateRetour);
        $commande->setModeRetrait($modeRetrait);
        $commande->setStatus('en_attente');
        $commande->setComentaireClient($commentaire);
        $commande->setCreatedAt(new \DateTimeImmutable());

        $totalHT = 0;
        $eventDetails = [];

        // 3. Création des EventDetail
        foreach ($cart as $productId => $item) {
            $product = $em->getRepository(Product::class)->find($productId);
            if (!$product) continue;

            $eventDetail = new EventDetail();
            $eventDetail->setUser($user);
            $eventDetail->setProduct($product);
            $eventDetail->setMouve('bc');
            $eventDetail->setQuantity($item['quantity']);
            $eventDetail->setDate(new \DateTime());
            $eventDetail->setCommande($commande);

            $totalHT += $item['price'] * $item['quantity'];
            $em->persist($eventDetail);
            $eventDetails[] = $eventDetail;
        }

        $commande->setTotalHT($totalHT);

        // 4. Génération du PDF
        $fileName = 'commande_' . $user->getId() . '_' . date('Ymd_His') . '.pdf';
        $filePath = $pdfGeneratorService->generatePdf(
            ['event_details' => $eventDetails, 'user' => $user, 'commande' => $commande],
            $fileName,
            'cart/pdf.html.twig',
            'uploads/bons_de_commande'
        );

        // 5. Enregistrement du chemin dans la commande
        $commande->setPdfPath($fileName); 

        // 6. Sauvegarde finale
        $em->persist($commande);
        $em->flush();

        // 7. Envoi de l'email
        $emailService->sendEmail(
            $user->getEmail(),
            file_get_contents($filePath),
            $fileName,
            ['user' => $user, 'event_details' => $eventDetails, 'commande' => $commande],
            'Votre bon de commande',
            'cart/email_confirmation.html.twig'
        );

        $session->remove('cart');
        $this->addFlash('success', 'Commande enregistrée avec succès. Bon de commande envoyé par email.');

        return $this->redirectToRoute('app_vente');

    } catch (\Throwable $e) {
        $this->addFlash('error', 'Erreur lors de la validation : ' . $e->getMessage());
        return $this->redirectToRoute('app_vente');
    }
}

}
