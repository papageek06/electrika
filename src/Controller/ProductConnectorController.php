<?php

namespace App\Controller;

use App\Entity\ProductConnector;
use App\Form\ProductConnectorForm;
use App\Repository\ProductConnectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product/connector')]
final class ProductConnectorController extends AbstractController
{
    #[Route(name: 'app_product_connector_index', methods: ['GET'])]
    public function index(ProductConnectorRepository $productConnectorRepository): Response
    {
        return $this->render('product_connector/index.html.twig', [
            'product_connectors' => $productConnectorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_connector_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productConnector = new ProductConnector();
        $form = $this->createForm(ProductConnectorForm::class, $productConnector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productConnector);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_connector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_connector/new.html.twig', [
            'product_connector' => $productConnector,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_connector_show', methods: ['GET'])]
    public function show(ProductConnector $productConnector): Response
    {
        return $this->render('product_connector/show.html.twig', [
            'product_connector' => $productConnector,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_connector_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductConnector $productConnector, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductConnectorForm::class, $productConnector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_connector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_connector/edit.html.twig', [
            'product_connector' => $productConnector,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_connector_delete', methods: ['POST'])]
    public function delete(Request $request, ProductConnector $productConnector, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productConnector->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($productConnector);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_connector_index', [], Response::HTTP_SEE_OTHER);
    }
}
