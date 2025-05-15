<?php

namespace App\Controller;

use App\Entity\Connector;
use App\Entity\Product;
use App\Entity\ProductConnector;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ConnectorRepository;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
final class ProductController extends AbstractController
{
  #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(
        ProductRepository $productRepository,
        EventRepository $eventRepository,
        EventDetailRepository $eventDetailRepository,
        EventRepository $event,
        SessionInterface $session,
        CategoryRepository $category
    ): Response {
        $products = $productRepository->findAll();
        $eventDetails = $eventDetailRepository->findAll();
        $stocksParProduit = [];
        $today = new \DateTimeImmutable();

        foreach ($products as $product) {
            $stockInitial = $product->getStockInitial();
            $stockParSemaine = [];

           for ($i = 1; $i <= 6; $i++) {
            $currentWeekDate = $today->modify('+' . ($i * 7) . ' days');
    $startDate = $today->modify('+' . (($i - 1) * 7) . ' days');
    $endDate = $startDate->modify('+6 days'); // intervalle sur 7 jours

    $bl = [];
    $bp = [];
    $new = [];

    foreach ($eventDetails as $ed) {
        if ($ed->getProduct()->getId() !== $product->getId()) {
            continue;
        }

        $montage = $ed->getEvent()->getDateMontage();
        $fin = $ed->getEvent()->getDateEnd();

        // Vérifie si l'événement chevauche la semaine en cours
        if ($montage <= $endDate && $fin >= $startDate) {
            switch ($ed->getMouve()) {
                case 'bl':
                    $bl[] = $ed;
                    break;
                case 'bp':
                    $bp[] = $ed;
                    break;
                case 'new':
                    $new[] = $ed;
                    break;
            }
        }
    }



                $stock = $stockInitial;
                $used = false;

                if (!empty($bl)) {
                    foreach ($bl as $ed) {
                        $stock -= $ed->getQuantity();
                    }
                    $used = true;
                } elseif (!empty($bp)) {
                    foreach ($bp as $ed) {
                        $stock -= $ed->getQuantity();
                    }
                    $used = true;
                } elseif (!empty($new)) {
                    foreach ($new as $ed) {
                        $stock -= $ed->getQuantity();
                    }
                    $used = true;
                }

                $stockParSemaine[] = [
                    'semaine' => $currentWeekDate->format('Y-m-d'),
                    'stock' => $used ? $stock : 'N/A'
                ];
            }

            $stocksParProduit[$product->getId()] = $stockParSemaine;
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'events' => $eventRepository->findAll(),
            'eventDetails' => $eventDetails,
            'retry' => $eventDetailRepository->countstockByProduct(),
            'cart_session' => $session->get('cart', []),
            'categorys' => $category->findAll(),
            'stockSemaines' => $stocksParProduit,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $productConnector = new ProductConnector();
        $product->addProductConnector($productConnector);
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
    public function show(Product $product, EventDetailRepository $eventDetailRepository , ConnectorRepository $connectorRepository): Response
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
            'connectors' => $connectorRepository->findAll(),

        ]);
    }



    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoProduct = $form->get('picture')->getData(); // récupère le fichier

            if ($photoProduct) {

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
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/add-connector', name: 'app_product_add_connector', methods: ['POST'])]
public function addConnector(
    Request $request,
    Product $product,
    EntityManagerInterface $em
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['connector_id'], $data['quantity'], $data['plugDirection'])) {
        return new JsonResponse(['error' => 'Données manquantes'], 400);
    }

    $connector = $em->getRepository(Connector::class)->find($data['connector_id']);
    if (!$connector) {
        return new JsonResponse(['error' => 'Connecteur introuvable'], 404);
    }

    $pc = new ProductConnector();
    $pc->setProduct($product);
    $pc->setConnector($connector);
    $pc->setQuantity((int)$data['quantity']);
    $pc->setPlugDirection($data['plugDirection']);

    $em->persist($pc);
    $em->flush();

    return new JsonResponse([
        'success' => true,
        'message' => 'Connecteur ajouté',
        'connectorLabel' => sprintf(' %dA %s', $connector->getPower(), $connector->getType()),
        'quantity' => $pc->getQuantity(),
        'plug' => $pc->getPlugDirection()
    ]);
}

}
