<?php

namespace App\Controller;

use App\Entity\EventDetail;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Twig\Environment;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {

     $user = $this->getUser(); // null si pas connecté
    $session = $request->getSession();

    // Détection du rôle
    if ($user && in_array('ROLE_TECHNICIEN', $user->getRoles())) {
        $cartKey = 'cart_technician';
    } else {
        $cartKey = 'cart_client';
    }

    $cartSession = $session->get($cartKey, []);

    if (isset($cartSession['idProduct']) && is_array($cartSession['idProduct']) && count($cartSession['idProduct']) > 0) {
        $cartSession['countCart'] = count($cartSession['idProduct']);
    } else {
        $cartSession = ["message" => "Le panier est vide"];
    }

    return $this->render('cart/index.html.twig', [
        'cart_session' => $cartSession,
        'cart_type' => $cartKey,
    ]);


    }

    #[Route('/cart/add', name: 'app_cart_add' , methods: ['POST'])]
    public function add(Request $request , ProductRepository $productRepository, EventRepository $eventRepository, UserRepository $userRepository): Response
    {

        $message="";
        $idEvent= $request->request->get('event_id');
        $quantity = $request->request->get('quantity');
        $idProduct = $request->request->get('product_id'); 
        $idUser = $request->request->get('user');

        if ($idEvent === null || $idEvent === "") {
            $this->addFlash('warning', 'Please select an event');
            return $this->redirectToRoute('app_product_index');
        }

        $session = $request->getSession();



  // Initialiser le panier s'il n'existe pas encore
if (!$session->get('cart')) {
            $session->set('cart', [
                "idProduct" => [],
                "nameProduct" => [],
                "descriptionProduct" => [],
                "stockProduct" => [],
                "quantity" => [],
                "eventId" => [],
                "userId" => [],
                "eventName" => [],
                "userName" => [],
                "message" => "",
                
                
            ]);
        }
        // je la récupère
        $cartSession = $session->get('cart');
        $found = false;
        for ($i = 0; $i < count($cartSession["idProduct"]); $i++) {
            if ($cartSession["idProduct"][$i] == $idProduct) {
                $cartSession["quantity"][$i] += $quantity; // Ajouter la quantité
                $found = true;
                break;
            }
        }
 
        if (!$found) {
        // je récupère les infos du produit en bdd que je souhaite ajouter à mon panier
        $product = $productRepository->find($idProduct);
        $event = $eventRepository->find($idEvent);
        $user = $userRepository->find($idUser);

        // j'alimente ma sessions panier avec les infos du produit

        $cartSession["idProduct"][] = $product->getId();
        $cartSession["nameProduct"][] = $product->getName();
        $cartSession["descriptionProduct"][] = $product->getDescription();
        $cartSession["stockProduct"][] = $product->getStock();
        $cartSession["quantity"][] = $quantity;
        $cartSession["eventId"][] = $event->getId();
        $cartSession["userId"][] = $user->getId();
        $cartSession["eventName"][] = $event->getName();
        $cartSession["userName"][] = $user->getfirstName();
        $cartSession["message"] = $message;
        }

        // mettre à jour la session
        $session->set('cart', $cartSession);

        return $this->redirectToRoute('app_product_index');
    }


    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session): Response
    {
        $session->remove('cart');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{index}', name: 'app_cart_remove')]
public function remove(SessionInterface $session, int $index): Response
{
    $cartSession = $session->get('cart', []);

    // Vérification que l'index existe bien dans le panier
    if (isset($cartSession["idProduct"][$index])) {
        // Supprimer l'élément dans tous les tableaux
        foreach ($cartSession as $key => $values) {
            if (is_array($values)) {
                array_splice($cartSession[$key], $index, 1);
            }
        }

        // Mise à jour de la session
        $session->set('cart', $cartSession);

        $this->addFlash('success', 'Produit retiré du panier.');
    } else {
        $this->addFlash('danger', 'Produit non trouvé dans le panier.');
    }

    return $this->redirectToRoute('app_cart');
}

#[Route('/cart/add/all', name: 'app_cart_add_all')]
public function addAll(SessionInterface $session ,
UserRepository $userRepository ,
ProductRepository $productRepository ,
EventRepository $eventRepository,
EventDetailRepository $eventDetailRepository,
EntityManagerInterface $entityManager
): Response
{
    $cart = $session->get('cart');

    if(isset($cart) && $cart['idProduct']>0 ){
        
        $cartCount= count($cart['idProduct']);

        for ( $i= 0 ; $i< $cartCount ; $i++ ){
            $user=$userRepository->find($cart['userId'][$i]);
            $product=$productRepository->find($cart['idProduct'][$i]);
            $event=$eventRepository->find($cart['eventId'][$i]);
            
            
            $EventDetail = new EventDetail;
            $EventDetail->setUser($user);
            $EventDetail->setProduct($product);
            $EventDetail->setEvent($event);
            $EventDetail->setMouve('new');
            $EventDetail->setQuantity($cart['quantity'][$i]);
            $EventDetail->setDate(new \DateTime());
            $entityManager->persist($EventDetail);
            

        }
      
        $entityManager->flush();
        $session->remove('cart');
        
    }
    return $this->redirectToRoute('app_event_index');
}
#[Route('/cart/technician/add', name: 'app_technician_cart_add', methods: ['POST'])]
public function addToTechnicianCart(
    Request $request,
    ProductRepository $productRepository,
    EventRepository $eventRepository
): Response {
    $session = $request->getSession();

    $productId = $request->request->get('product_id');
    $quantity = (int) $request->request->get('quantity');
    $eventId = $request->request->get('event_id');
    $status = $request->request->get('status'); // peut être 'new', 'bp', etc.

    // Sécurité
    if (!$productId || !$eventId || $quantity < 1) {
        $this->addFlash('error', 'Informations incomplètes pour le panier');
        return $this->redirectToRoute('app_product_index');
    }

    $product = $productRepository->find($productId);
    $event = $eventRepository->find($eventId);

    if (!$product || !$event) {
        $this->addFlash('error', 'Produit ou évènement introuvable');
        return $this->redirectToRoute('app_product_index');
    }

    // Panier technicien en session
    $cart = $session->get('cart_technician', []);
    $cart['status'] = $status;
    $cart['eventId'] = $event->getId();
    $cart['eventName'] = $event->getName();

    // Initialiser le tableau s’il n'existe pas
    if (!isset($cart['products'])) {
        $cart['products'] = [];
    }

    // Ajouter ou mettre à jour
    $found = false;
    foreach ($cart['products'] as &$item) {
        if ($item['product']->getId() === $product->getId()) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $cart['products'][] = [
            'product' => $product,
            'quantity' => $quantity,
            'event' => $event,
        ];
    }

    $session->set('cart_technician', $cart);

    
if ($request->isXmlHttpRequest()) {
    $cartHtml = $this->renderView('cart/_technician_dropdown.html.twig', [
        'cart' => $cart,
    ]);

    return $this->json([
        'success' => true,
        'message' => "{$product->getName()} ajouté au panier",
        'cartCount' => count($cart['products']),
        'cartHtml' => $cartHtml,
    ]);
}
// Si ce n'est pas une requête AJAX, on redirige
$this->addFlash('success', "{$product->getName()} ajouté au panier technique.");
return $this->redirectToRoute('app_product_index');

}

#[Route('/cart/technician/validate', name: 'app_technician_cart_validate')]
public function validateTechnicianCart(
    SessionInterface $session,
    ProductRepository $productRepository,
    EventRepository $eventRepository,
    EntityManagerInterface $em
): Response {
    $cart = $session->get('cart_technician');

    if (!$cart || empty($cart['products'])) {
        $this->addFlash('error', 'Aucun produit dans le panier technicien.');
        return $this->redirectToRoute('app_product_index');
    }

    $event = $eventRepository->find($cart['eventId']);
    if (!$event) {
        $this->addFlash('error', 'Évènement non trouvé.');
        return $this->redirectToRoute('app_product_index');
    }

    foreach ($cart['products'] as $item) {
        $product = $productRepository->find($item['product']->getId());
        if (!$product) {
            continue; // Ignore les produits supprimés ou introuvables
        }

        $eventDetail = new EventDetail();
        $eventDetail->setProduct($product);
        $eventDetail->setEvent($event);
        $eventDetail->setQuantity($item['quantity']);
        $eventDetail->setMouve($cart['status']);
        $eventDetail->setDate(new \DateTime());

        $em->persist($eventDetail);
    }

    $em->flush();
    $session->remove('cart_technician');

    $this->addFlash('success', 'Préparation validée pour l\'évènement.');
    return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
}

#[Route('/cart/technician/remove/{key}', name: 'app_technician_cart_remove')]
public function removeTechnicianItem(SessionInterface $session, int $key): Response
{
    $cart = $session->get('cart_technician', []);
    if (isset($cart['products'][$key])) {
        unset($cart['products'][$key]);
        $cart['products'] = array_values($cart['products']); // Réindexation
        $session->set('cart_technician', $cart);
    }

    return $this->redirectToRoute('app_product_index'); // ou autre route
}



    #[Route('/cart/technician/clear', name: 'app_technician_cart_clear')]
public function clearTechnicianCart(SessionInterface $session): Response
{
    $session->remove('cart_technician');
    return $this->redirectToRoute('app_product_index');
}

#[Route('/cart/technician/update', name: 'app_technician_cart_update_quantity', methods: ['POST'])]
public function updateTechnicianQuantity(Request $request, SessionInterface $session): JsonResponse
{
    $key = (int) $request->request->get('key');
    $quantity = (int) $request->request->get('quantity');

    $cart = $session->get('cart_technician', []);
    if (isset($cart['products'][$key]) && $quantity > 0) {
        $cart['products'][$key]['quantity'] = $quantity;
        $session->set('cart_technician', $cart);
        return new JsonResponse(['success' => true, 'message' => 'Quantité mise à jour']);
    }

    return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}

#[Route('/cart/technician/bulk-add', name: 'app_technician_cart_bulk_add', methods: ['POST'])]
public function bulkAddToTechnicianCart(
    Request $request,
    EventRepository $eventRepository
): Response {
    $eventId = $request->request->get('event_id');
    $status = $request->request->get('status');

    $session = $request->getSession();

    $event = $eventRepository->find($eventId);
    if (!$event) {
        $this->addFlash('error', "Événement introuvable.");
        return $this->redirectToRoute('app_event_index');
    }

    // On ne garde que les informations de contexte
    $cart = [
        'status' => $status,
        'eventId' => $eventId,
        'eventName' => $event->getName(),
        'products' => [] // vide, on le remplit plus tard manuellement
    ];

    $session->set('cart_technician', $cart);

    $this->addFlash('success', 'Événement sélectionné pour le panier technicien.');
    return $this->redirectToRoute('app_product_index'); // vers la page d’ajout produit
}

  
        #[Route('/cart/technician/dropdown', name: 'app_technician_cart_dropdown', methods: ['GET'])]
public function technicianDropdown(
    SessionInterface $session,
    ProductRepository $productRepository
): Response {
    $cart = $session->get('cart_technician', []);

    // Si tu stockes juste les IDs (meilleure pratique)
    if (isset($cart['products'])) {
        foreach ($cart['products'] as $index => $item) {
            if (isset($item['product_id']) && !isset($item['product'])) {
                $product = $productRepository->find($item['product_id']);
                if ($product) {
                    $cart['products'][$index]['product'] = $product;
                }
            }
        }
    }

    return $this->render('cart/_technician_dropdown.html.twig', [
        'cart' => $cart,
    ]);
}
        

}
