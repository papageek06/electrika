<?php

namespace App\Controller;

use App\Entity\GaleryPicture;
use App\Form\GaleryPictureType;
use App\Repository\GaleryPictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/galery/picture')]
final class GaleryPictureController extends AbstractController
{
    #[Route(name: 'app_galery_picture_index', methods: ['GET'])]
    public function index(GaleryPictureRepository $galeryPictureRepository): Response
    {
        return $this->render('galery_picture/index.html.twig', [
            'galery_pictures' => $galeryPictureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_galery_picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        
        $galeryPicture = new GaleryPicture();
        $form = $this->createForm(GaleryPictureType::class, $galeryPicture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('picture')->getData(); // récupère le fichier

            if($photo) {

                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // Récupère le nom original du fichier sans son extension.
                
                $safeFilename = $slugger->slug($originalFilename);
                // Transforme le nom original en une version sécurisée (supprime les caractères spéciaux, espaces, etc.).
                
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                // Génère un nom de fichier unique en ajoutant un identifiant unique (`uniqid()`) et en conservant l'extension d'origine.
               
                try {
                    $photo->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                    $galeryPicture->setPicture($newFilename); // Mise à jour du champ `picture` dans l'entité

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }

            }

            $entityManager->persist($galeryPicture);
            $entityManager->flush();

            return $this->redirectToRoute('app_galery_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galery_picture/new.html.twig', [
            'galery_picture' => $galeryPicture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galery_picture_show', methods: ['GET'])]
    public function show(GaleryPicture $galeryPicture): Response
    {
        return $this->render('galery_picture/show.html.twig', [
            'galery_picture' => $galeryPicture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_galery_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GaleryPicture $galeryPicture, EntityManagerInterface $entityManager, SluggerInterface $slugger ): Response
    {
        
        $form = $this->createForm(GaleryPictureType::class, $galeryPicture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('picture')->getData(); // récupère le fichier

            if($photo) {

                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // Récupère le nom original du fichier sans son extension.
                
                $safeFilename = $slugger->slug($originalFilename);
                // Transforme le nom original en une version sécurisée (supprime les caractères spéciaux, espaces, etc.).
                
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                // Génère un nom de fichier unique en ajoutant un identifiant unique (`uniqid()`) et en conservant l'extension d'origine.
               
                try {
                    $photo->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                    $galeryPicture->setPicture($newFilename); // Mise à jour du champ `picture` dans l'entité

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }

            }

            $entityManager->flush();

            return $this->redirectToRoute('app_galery_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galery_picture/edit.html.twig', [
            'galery_picture' => $galeryPicture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galery_picture_delete', methods: ['POST'])]
    public function delete(Request $request, GaleryPicture $galeryPicture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$galeryPicture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($galeryPicture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_galery_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
