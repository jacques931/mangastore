<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->json(['success' => false, 'message' => 'Product ID is required'], 400);
        }

        $cart = $session->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);

        return $this->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => array_sum($cart)
        ]);
    }

    #[Route('/update', name: 'update', methods: ['POST'])]
    public function update(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? null;

        if (!$productId || $quantity === null || $quantity < 1) {
            return $this->json(['success' => false, 'message' => 'Invalid request data'], 400);
        }

        $cart = $session->get('cart', []);
        
        if (!isset($cart[$productId])) {
            return $this->json(['success' => false, 'message' => 'Product not found in cart'], 404);
        }

        $cart[$productId] = $quantity;
        $session->set('cart', $cart);

        return $this->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cartCount' => array_sum($cart)
        ]);
    }

    #[Route('/remove', name: 'app_api_cart_remove', methods: ['POST'])]
    public function remove(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;

        if (!$productId) {
            return new JsonResponse(['error' => 'Product ID is required', 'reload' => false], Response::HTTP_BAD_REQUEST);
        }

        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $session->set('cart', $cart);

            $reload = empty($cart);

            return new JsonResponse([
                'success' => true,
                'message' => 'Produit supprimé du panier',
                'reload' => $reload
            ]);
        }

        return new JsonResponse([
            'error' => 'Produit non trouvé dans le panier',
            'reload' => false
        ], Response::HTTP_NOT_FOUND);
    }
}
