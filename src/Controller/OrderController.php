<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/cart', name: 'app_order_cart')]
    public function showCart(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPriceHt() * $quantity;
            }
        }

        return $this->render('order/cart.html.twig', [
            'items' => $cartData,
            'total' => $total
        ]);
    }

    #[Route('/confirm', name: 'app_order_confirm', methods: ['POST'])]
    public function confirmOrder(
        SessionInterface $session,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour passer une commande.');
            return $this->redirectToRoute('app_login');
        }

        $cart = $session->get('cart', []);
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }

        $order = new Order();
        $order->setCustomer($user);
        $order->setDateTime(new \DateTime());
        $order->setValid(true);
        
        // Générer un numéro de commande unique (timestamp + id utilisateur)
        $orderNumber = time() . '-' . $user->getId();
        $order->setOrderNumber($orderNumber);

        $shippingCost = 4.99;
        $total = $shippingCost;

        // Ajouter les produits à la commande
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($product);
                $orderProduct->setQuantity($quantity);
                $orderProduct->setLinkOrder($order);
                $total += $product->getPriceHt() * $quantity;
                $entityManager->persist($orderProduct);
            }
        }
        $order->setTotal($total);
        $entityManager->persist($order);
        $entityManager->flush();

        // Vider le panier
        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a été confirmée avec succès !');
        return $this->redirectToRoute('app_order_show', ['order_number' => $orderNumber]);
    }

    #[Route('/', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos commandes.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les commandes de l'utilisateur
        $orders = $orderRepository->findBy(
            ['customer' => $user],
            ['date_time' => 'DESC']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/{order_number}', name: 'app_order_show')]
    public function showOrder(string $order_number, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneBy(['order_number' => $order_number]);
        
        if (!$order) {
            throw new NotFoundHttpException('Commande non trouvée.');
        }

        // Vérifier si l'utilisateur est connecté et est propriétaire de la commande
        $user = $this->getUser();
        if (!$user || $order->getCustomer() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }
}
