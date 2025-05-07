<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository,EventRepository $eventRepository,EventDetailRepository $eventDetail , EventRepository $event, SessionInterface $session, CategoryRepository $category): Response
    {
        $retry = $eventDetail->countstockByProduct();


       
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'events' => $eventRepository->findAll(),
            'eventDetails' => $eventDetail->findAll(),
            'retry' => $retry,
            'cart_session' => $session->get('cart', []),
            'categorys' => $category->findAll()
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

       
        #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
        public function show(Product $product, EventDetailRepository $eventDetailRepository): Response
        {
            
            $eventDetails = $eventDetailRepository->findByProduct($product->getId());
            $calendarEvents = [];
            $productId = $product->getId();
            $stockInitial = $product->getStockInitial();
            
            $eventQuantities = []; // [eventId => total quantity]
            
            foreach ($eventDetails as $detail) {
                // On ne garde que les lignes du bon produit + mouvement = 'new'
                if (
                    $detail->getProduct()->getId() === $productId &&
                    $detail->getMouve() === 'new'
                ) {
                    $eventId = $detail->getEvent()->getId();
                    if (!isset($eventQuantities[$eventId])) {
                        $eventQuantities[$eventId] = [
                            'event' => $detail->getEvent(),
                            'total' => 0
                        ];
                    }
            
                    $eventQuantities[$eventId]['total'] += $detail->getQuantity();
                }
            }
            
            // Formatage pour FullCalendar
            foreach ($eventQuantities as $eventData) {
                $event = $eventData['event'];
                $quantity = $eventData['total'];
            
                $start = $event->getDateMontage();
                $end = (clone $event->getDateEnd())->modify('+1 day');
            
                $calendarEvents[] = [
                    'title' => $event->getName() . " - $quantity / $stockInitial",
                    'start' => $start->format('Y-m-d'),
                    'end'   => $end->format('Y-m-d'),
                    'color' => $quantity > $stockInitial ? '#dc3545' : '#28a745'
                ];
            }
            
            return $this->render('product/show.html.twig', [
                'product' => $product,
                'calendarEvents' => $calendarEvents,
    
            ]);
    
        }

    

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoProduct = $form->get('picture')->getData(); // récupère le fichier

            if($photoProduct) {

                $originalFilename = pathinfo($photoProduct->getClientOriginalName(), PATHINFO_FILENAME);
                // Récupère le nom original du fichier sans son extension.
                
                $safeFilename = $slugger->slug($originalFilename);
                // Transforme le nom original en une version sécurisée (supprime les caractères spéciaux, espaces, etc.).
                
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoProduct->guessExtension();
                // Génère un nom de fichier unique en ajoutant un identifiant unique (`uniqid()`) et en conservant l'extension d'origine.

                try {
                    $photoProduct->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                    $product->setPicture($newFilename); // Mise à jour du champ `picture` dans l'entité

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }

            }
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
