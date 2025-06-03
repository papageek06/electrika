<?php

namespace App\Controller;

use App\Entity\InterventionTeam;
use App\Form\InterventionTeamForm;
use App\Repository\InterventionTeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/intervention/team')]
final class InterventionTeamController extends AbstractController
{
    #[Route(name: 'app_intervention_team_index', methods: ['GET'])]
    public function index(InterventionTeamRepository $interventionTeamRepository): Response
    {
        return $this->render('intervention_team/index.html.twig', [
            'intervention_teams' => $interventionTeamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_intervention_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $interventionTeam = new InterventionTeam();
        $form = $this->createForm(InterventionTeamForm::class, $interventionTeam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($interventionTeam);
            $entityManager->flush();

            return $this->redirectToRoute('app_intervention_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('intervention_team/new.html.twig', [
            'intervention_team' => $interventionTeam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_intervention_team_show', methods: ['GET'])]
    public function show(InterventionTeam $interventionTeam): Response
    {
        return $this->render('intervention_team/show.html.twig', [
            'intervention_team' => $interventionTeam,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_intervention_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InterventionTeam $interventionTeam, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InterventionTeamForm::class, $interventionTeam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_intervention_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('intervention_team/edit.html.twig', [
            'intervention_team' => $interventionTeam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_intervention_team_delete', methods: ['POST'])]
    public function delete(Request $request, InterventionTeam $interventionTeam, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$interventionTeam->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($interventionTeam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_intervention_team_index', [], Response::HTTP_SEE_OTHER);
    }
}
