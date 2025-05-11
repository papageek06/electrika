<?php

namespace App\Controller;

use App\Entity\SpareParts;
use App\Form\SparePartsForm;
use App\Repository\SparePartsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/spare/parts')]
final class SparePartsController extends AbstractController
{
    #[Route(name: 'app_spare_parts_index', methods: ['GET'])]
    public function index(SparePartsRepository $sparePartsRepository): Response
    {
        return $this->render('spare_parts/index.html.twig', [
            'spare_parts' => $sparePartsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_spare_parts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sparePart = new SpareParts();
        $form = $this->createForm(SparePartsForm::class, $sparePart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sparePart);
            $entityManager->flush();

            return $this->redirectToRoute('app_spare_parts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('spare_parts/new.html.twig', [
            'spare_part' => $sparePart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_spare_parts_show', methods: ['GET'])]
    public function show(SpareParts $sparePart): Response
    {
        return $this->render('spare_parts/show.html.twig', [
            'spare_part' => $sparePart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_spare_parts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SpareParts $sparePart, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SparePartsForm::class, $sparePart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_spare_parts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('spare_parts/edit.html.twig', [
            'spare_part' => $sparePart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_spare_parts_delete', methods: ['POST'])]
    public function delete(Request $request, SpareParts $sparePart, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sparePart->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sparePart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_spare_parts_index', [], Response::HTTP_SEE_OTHER);
    }
}
