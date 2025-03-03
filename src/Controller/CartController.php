<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

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
    
        $message ="";
        // dd($request->request->get('event_id'));
        if($request->request->get('eventId') == null && $request->request->get('event_id') == "select your event" ){
           
            $message = "Please select an event";
            return $this->redirectToRoute('app_product_index');
        }
        $idEvent= $request->request->get('event_id');
        $quantity = $request->request->get('quantity');
        $idProduct = $request->request->get('product_id'); 
        $idUser = $request->request->get('user');


        $session = $request->getSession();
        // if (!$session->get('event')) {
        //     $session->set('event', [
        //         $idEvent => [
        //             'articles' => [
        //                 $idProduct => [
        //                     'quantity' => $quantity,
        //                     'date_sortie' => '2021-10-10',                 
        //                 ],
        //             ],
        //             'user' => [
        //                 'id' => $idUser,
        //             ],
        //         ],
        //     ]);
        // }

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

        // mettre à jour la session
        $session->set('cart', $cartSession);

        return $this->redirectToRoute('app_cart');
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

    

    
                

}
