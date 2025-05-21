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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {

        $session = $request->getSession();
        $cartSession = $session->get('cart');
        
        if (!is_null($cartSession) && count($cartSession["idProduct"]) > 0) {

           $countCart = count($cartSession["idProduct"]);
           $cartSession["countCart"] = $countCart;

        }else{
            $cartSession = ["message" => "Your cart is empty"];
        }


        return $this->render('cart/index.html.twig', [
            'cart_session' => $cartSession,
            
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

    

    
                

}
