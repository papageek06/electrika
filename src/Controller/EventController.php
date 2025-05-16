<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventDetail;
use App\Form\EventType;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Service\EmailService;
use App\Service\PdfGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event, EventDetailRepository $eventDetailRepository): Response
    {
        $eventDetails = $eventDetailRepository->findBy(['event' => $event->getId()]);
        $status = 0;
        $mouvePriority = [
            'new' => 0,
            'bp' => 1,
            'bl' => 2,
            'br' => 3,

        ];

        foreach ($eventDetails as $eventDetail) {
            $mouve = $eventDetail->getMouve();

            if (isset($mouvePriority[$mouve])) {
                $currentPriority = $mouvePriority[$mouve];


                if ($currentPriority > $status) {
                    $status = $currentPriority;
                }
            }
        }
        // ------------------ afficher les pdf lier ------------------

        $finder = new Finder();
        $finder->files()
            ->in($this->getParameter('kernel.project_dir') . '/public/uploads/invoices')
            ->name('/^' . $event->getId() . '_.*\.pdf$/');

        $pdfFiles = [];

        foreach ($finder as $file) {
            $pdfFiles[] = 'uploads/invoices/' . $file->getFilename();
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'eventDetails' => $eventDetails,
            'site' => $event->getSite(),
            'status' => $status,
            'pdfFiles' => $pdfFiles,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/app_event_upgrade/{id}', name: 'app_event_upgrade')]
    public function upgrade(Request $request, EventDetailRepository $eventDetailRepository, int $id, EventRepository $eventRepository, EntityManagerInterface $entityManager, PdfGeneratorService $pdfGeneratorService, EmailService $emailService): Response
    {
        $event = $eventRepository->find($id);
        $status = $request->query->get('status');


        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }
        $eventDetails = $eventDetailRepository->findBy(['event' => $event]);

       

        foreach ($eventDetails as $eventDetail) {


            if ($status == 'bp' && $eventDetail->getMouve() === 'new') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event);
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('bp');
                $entityManager->persist($newDetail);
                $entityManager->flush();
            } else if ($status == 'bl' && $eventDetail->getMouve() === 'bp') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event);
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('bl');
                $entityManager->persist($newDetail);
                $entityManager->flush();
            } else if ($status == 'br' && $eventDetail->getMouve() === 'bl') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event);
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('br');
                $entityManager->persist($newDetail);
                $entityManager->flush();
            } else if ($status == 'bf' && $eventDetail->getMouve() === 'bl') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event);
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('bf');
                $entityManager->persist($newDetail);
                $entityManager->flush();
            }
        }
        
$fileName =$event->getId() . "_" . $status."_" . time() . ".pdf";
try {
    
   
    $pdfPath = $pdfGeneratorService->generatePdf(
        [
            'user' => $this->getUser(),
            'date' => new \DateTime(),
            'event_details' => $eventDetailRepository->findBy(['event' => $event, 'mouve' => $status])
        ],
        $fileName,
        'event_detail/pdf_send.html.twig',
         '/uploads/invoices/'
    );
    $this->addFlash('success', 'PDF généré avec succès.');
} catch (\Throwable $th) {
    $this->addFlash('error', 'Erreur lors de la génération du PDF.');
    return $this->redirectToRoute('app_event_show', ['id' => $id]);
}



    try {
    if (!file_exists($pdfPath)) {
        throw new \RuntimeException("Fichier PDF introuvable : $pdfPath");
    }

    $emailService->sendEmail(
        $this->getUser()->getUserIdentifier(),
        $pdfPath,
        basename($pdfPath), // nom du fichier
        [
            'user' => $this->getUser(),
            'date' => new \DateTime(),
            'event_details' => $eventDetailRepository->findBy([
    'event' => $event,
    'mouve' => $status
]),
            'event' => $event
        ],
        "Mouvement de commande !",
        "email/order_send.html.twig"
    );

    $this->addFlash('success', 'Mail envoyé avec succès.');
} catch (\Throwable $th) {
    $this->addFlash('error', 'Erreur lors de l\'envoi du mail : ' . $th->getMessage());
    return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
}



        return $this->redirectToRoute('app_event_show', ['id' => $id]);
    }

    #[Route('/{id}/update-quantities', name: 'app_event_quantity_update', methods: ['POST'])]
public function updateQuantities(Request $request, Event $event, EntityManagerInterface $em): Response
{
    $quantities = $request->request->all('quantities');

    foreach ($quantities as $id => $quantity) {
        $detail = $em->getRepository(EventDetail::class)->find($id);
        if ($detail && $detail->getEvent() === $event) {
            $detail->setQuantity((int)$quantity);
        }
    }

    $em->flush();
    $this->addFlash('success', 'Quantités mises à jour.');

    return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
}




}
