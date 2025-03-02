<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class PrepaController extends AbstractController
{
    #[Route('/prepa', name: 'app_prepa')]
    public function index(Request $request): Response
    {

        $session = $request->getSession();


        return $this->render('prepa/index.html.twig', [
            'prepa_items' => $session->get('prepa'),
        ]);
    }

    #[Route('/prepa/{id}', name: 'app_add_prepa', methods: ['GET'])]
    public function addArticleToPrepa(int $id, Request $request, ProductRepository $productRepository): Response
    {

        // créer la session
        $session = $request->getSession();

        // si elle existe pas je la créé
        if (!$session->get('prepa')) {
            $session->set('prepa', [
                "id" => [],
                "name" => [],
                "stock_initial" => [],
                "stock" => [],
                "hs" => [],
                "lost" => [],
                "category" => [],
                "description" => [],
            ]);
        }

         // je la récupère
         $prepaSession = $session->get('prepa');

         // je récupère les infos du produit en bdd que je souhaite ajouter à mon panier
         $product = $productRepository->find($id);
 
         // j'alimente ma sessions panier avec les infos du produit
 
         $cartSession["id"][] = $product->getId();
         $cartSession["name"][] = $product->getName();
         $cartSession["stock_initial"][] = $product->getStockInitial();
         $cartSession["stock"][] = $product->getStock();
         $cartSession["hs"][] = $product->getHs();
         $cartSession["lost"][] = $product->getLost();
         $cartSession["category"][] = $product->getCategory();
         $cartSession["quantity"][] = 1;
         $cartSession["description"][] = $product->getDescription();
 
         // mettre à jour la session
         $session->set('prepa', $prepaSession);
 
         return $this->redirectToRoute('app_prepa');
}
}