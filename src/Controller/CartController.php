<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {

        $session = $request->getSession();
        $cartSession = $session->get('cart');
        
        if (!is_null($cartSession) && count($cartSession["id"]) > 0) {

           $countCart = count($cartSession["id"]);
           $cartSession["countCart"] = $countCart;

        }else{
            $cartSession = ["message" => "Your cart is empty"];
        }

        return $this->render('cart/index.html.twig', [
            'cart_items' => $cartSession,
            
        ]);

    }

    #[Route('/cart/add', name: 'app_cart_add' , methods: ['POST'])]
    public function add(Request $request , ProductRepository $productRepository): Response
    {
    
        
        $idEvent= $request->request->get('event_id');
        $quantity = $request->request->get('quantity');
        $idProduct = $request->request->get('product_id'); 
        $user = $request->request->get('user');


        $session = $request->getSession();
        if (!$session->get('event')) {
            $session->set('event', [
                $idEvent => [
                    'articles' => [
                        $idProduct => [
                            'quantity' => $quantity,
                            'date_sortie' => '2021-10-10',                 
                        ],
                    ],
                    'user' => [
                        'id' => $user,
                    ],
                ],
            ]);
        }

        dd($session->get('event'));

    }
                

}
