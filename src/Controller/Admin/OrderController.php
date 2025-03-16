<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_admin_orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_orders_show')]
    public function show(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_orders_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            // Supprime d'abord les order_products associés
            foreach ($order->getOrderProducts() as $orderProduct) {
                $entityManager->remove($orderProduct);
            }
            // Puis supprime la commande
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_orders');
    }
}
