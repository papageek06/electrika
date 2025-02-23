<?php

namespace App\Controller;

use App\Entity\EventDetail;
use App\Form\EventDetailType;
use App\Repository\EventDetailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/eventdetail')]
final class EventDetailController extends AbstractController
{
    #[Route(name: 'app_eventdetail_index', methods: ['GET'])]
    public function index(EventDetailRepository $eventDetailRepository): Response
    {
        return $this->render('event_detail/index.html.twig', [
            'event_details' => $eventDetailRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_detail_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventDetail = new EventDetail();
        $form = $this->createForm(EventDetailType::class, $eventDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventDetail);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_detail/new.html.twig', [
            'event_detail' => $eventDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_detail_show', methods: ['GET'])]
    public function show(EventDetail $eventDetail): Response
    {
        return $this->render('event_detail/show.html.twig', [
            'event_detail' => $eventDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_detail_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EventDetail $eventDetail, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventDetailType::class, $eventDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_detail/edit.html.twig', [
            'event_detail' => $eventDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_detail_delete', methods: ['POST'])]
    public function delete(Request $request, EventDetail $eventDetail, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventDetail->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($eventDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
