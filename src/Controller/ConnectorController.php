<?php

namespace App\Controller;

use App\Entity\Connector;
use App\Form\ConnectorForm;
use App\Repository\ConnectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/connector')]
final class ConnectorController extends AbstractController
{
    #[Route(name: 'app_connector_index', methods: ['GET'])]
    public function index(ConnectorRepository $connectorRepository): Response
    {
        return $this->render('connector/index.html.twig', [
            'connectors' => $connectorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_connector_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $connector = new Connector();
        $form = $this->createForm(ConnectorForm::class, $connector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($connector);
            $entityManager->flush();

            return $this->redirectToRoute('app_connector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('connector/new.html.twig', [
            'connector' => $connector,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_connector_show', methods: ['GET'])]
    public function show(Connector $connector): Response
    {
        return $this->render('connector/show.html.twig', [
            'connector' => $connector,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_connector_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Connector $connector, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConnectorForm::class, $connector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_connector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('connector/edit.html.twig', [
            'connector' => $connector,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_connector_delete', methods: ['POST'])]
    public function delete(Request $request, Connector $connector, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$connector->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($connector);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_connector_index', [], Response::HTTP_SEE_OTHER);
    }
}
