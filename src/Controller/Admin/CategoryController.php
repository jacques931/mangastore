<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Media;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories')]
class CategoryController extends AbstractController
{
    private $mediaService;

    public function __construct(
        private EntityManagerInterface $entityManager,
        MediaService $mediaService
    ) {
        $this->mediaService = $mediaService;
    }

    #[Route('/', name: 'app_admin_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_categories_new')]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('media_file')->getData();
            // Upload image
            if ($imageFile) {
                $media = $this->mediaService->uploadMedia($imageFile);
                if ($media) {
                    $category->setMedia($media);
                    $this->entityManager->persist($media);
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categories/manage.html.twig', [
            'category' => $category,
            'is_edit' => false,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_categories_edit')]
    public function edit(Category $category, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('media_file')->getData();
            // Upload image
            if ($imageFile) {
                $media = $this->mediaService->uploadMedia($imageFile);
                if ($media) {
                    $category->setMedia($media);
                    $this->entityManager->persist($media);
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categories/manage.html.twig', [
            'category' => $category,
            'is_edit' => true,  
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_categories_delete', methods: ['POST'])]
    public function delete(Category $category): Response
    {
        if ($category->getMedia()) {
            $this->mediaService->deleteMedia($category->getMedia());
            $this->entityManager->remove($category->getMedia());
        }
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès');
        return $this->redirectToRoute('app_admin_categories');
    }
}
