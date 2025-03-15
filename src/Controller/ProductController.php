<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'category' => null,
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/category/{id}/products', name: 'app_category_products')]
    public function categoryProducts(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        return $this->render('product/index.html.twig', [
            'category' => $category,
            'products' => $category->getProducts(),
        ]);
    }
}
