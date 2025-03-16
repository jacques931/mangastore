<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Media;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/products')]
class ProductController extends AbstractController
{
    private $mediaService;

    public function __construct(
        private EntityManagerInterface $entityManager,
        MediaService $mediaService
    ) {
        $this->mediaService = $mediaService;
    }

    #[Route('/', name: 'app_admin_products')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_products_new')]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('media_file')->getData();
            // Upload image
            if ($imageFile) {
                $media = $this->mediaService->uploadMedia($imageFile);
                if ($media) {
                    $product->setMedia($media);
                    $this->entityManager->persist($media);
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit créé avec succès');
            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/products/manage.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
            'product' => null,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_products_edit')]
    public function edit(Product $product, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('media_file')->getData();
            // Upload image
            if ($imageFile) {
                $media = $this->mediaService->uploadMedia($imageFile);
                if ($media) {
                    $product->setMedia($media);
                    $this->entityManager->persist($media);
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }
            
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/products/manage.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
            'product' => $product,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_products_delete', methods: ['POST'])]
    public function delete(Product $product): Response
    {
        if ($product->getMedia()) {
            $this->mediaService->deleteMedia($product->getMedia());
            $this->entityManager->remove($product->getMedia());
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès');
        return $this->redirectToRoute('app_admin_products');
    }
}
