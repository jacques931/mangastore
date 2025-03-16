<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderProductRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository, OrderProductRepository $orderProductRepository): Response
    {
        // Récupérer les 3 catégories les plus vendues
        $topCategories = $categoryRepository->findTopSellingCategories(3);
        
        // Récupérer les 3 produits les plus vendus
        $topProducts = $productRepository->findTopSellingProducts(3);

        return $this->render('home/index.html.twig', [
            'topCategories' => $topCategories,
            'topProducts' => $topProducts,
        ]);
    }
}
