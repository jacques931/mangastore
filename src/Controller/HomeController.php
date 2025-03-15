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
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository $productRepository,
        private OrderProductRepository $orderProductRepository
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Récupérer les 3 catégories les plus vendues
        $topCategories = $this->categoryRepository->findTopSellingCategories(3);
        
        // Récupérer les 3 produits les plus vendus
        $topProducts = $this->productRepository->findTopSellingProducts(3);

        return $this->render('home/index.html.twig', [
            'topCategories' => $topCategories,
            'topProducts' => $topProducts,
        ]);
    }
}
