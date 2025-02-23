<?php

namespace App\Controller;

use App\Entity\SiteEvent;
use App\Form\SiteEventType;
use App\Repository\SiteEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/site/event')]
final class SiteEventController extends AbstractController
{
    #[Route(name: 'app_site_event_index', methods: ['GET'])]
    public function index(SiteEventRepository $siteEventRepository): Response
    {
        return $this->render('site_event/index.html.twig', [
            'site_events' => $siteEventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_site_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $siteEvent = new SiteEvent();
        $form = $this->createForm(SiteEventType::class, $siteEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($siteEvent);
            $entityManager->flush();

            return $this->redirectToRoute('app_site_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site_event/new.html.twig', [
            'site_event' => $siteEvent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_event_show', methods: ['GET'])]
    public function show(SiteEvent $siteEvent): Response
    {
        return $this->render('site_event/show.html.twig', [
            'site_event' => $siteEvent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_site_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SiteEvent $siteEvent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SiteEventType::class, $siteEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_site_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site_event/edit.html.twig', [
            'site_event' => $siteEvent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_event_delete', methods: ['POST'])]
    public function delete(Request $request, SiteEvent $siteEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$siteEvent->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($siteEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_site_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
