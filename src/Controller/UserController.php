<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData(); // récupère l'utilisateur à partir du formulaire
            $photoUser = $form->get('picture')->getData(); // récupère le fichier

            if ($photoUser) {

                $originalFilename = pathinfo($photoUser->getClientOriginalName(), PATHINFO_FILENAME);
                // Récupère le nom original du fichier sans son extension.

                $safeFilename = $slugger->slug($originalFilename);
                // Transforme le nom original en une version sécurisée (supprime les caractères spéciaux, espaces, etc.).

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoUser->guessExtension();
                // Génère un nom de fichier unique en ajoutant un identifiant unique (`uniqid()`) et en conservant l'extension d'origine.

                try {
                    $photoUser->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                    $user->setPicture($newFilename); // Mise à jour du champ `picture` dans l'entité

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/user/{id}/change-password', name: 'app_user_change_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        User $targetUser,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
    
        // Sécurité : seul l'utilisateur lui-même ou un admin peut modifier
        if ($currentUser !== $targetUser && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez modifier que votre propre mot de passe.');
            return $this->redirectToRoute('app_user_show', ['id' => $currentUser->getId()]);
        }
    
        $pass1 = $request->request->get('password1');
        $pass2 = $request->request->get('password2');
    
        if (!$pass1 || !$pass2 || strlen($pass1) < 6) {
            $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
        } elseif ($pass1 !== $pass2) {
            $this->addFlash('error', 'Les deux mots de passe ne correspondent pas.');
        } else {
            $hashedPassword = $passwordHasher->hashPassword($targetUser, $pass1);
            $targetUser->setPassword($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
        }
    
        return $this->redirectToRoute('app_user_show', ['id' => $targetUser->getId()]);
    }
    
}
