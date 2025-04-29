<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VenteController extends AbstractController
{
    #[Route('/vente', name: 'app_vente')]
    public function index(ProductRepository $product): Response
    {
        $articles = $product->findAll();

        return $this->render('vente/index.html.twig', [
            'controller_name' => 'VenteController',
            'articles' => $articles,
        ]);
    }
}
