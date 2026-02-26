<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $repo): Response
    {
        $categories = $repo->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();
        
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Your category was created with success');

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/newCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'app_category_edit')]
    public function editCategory(Category $category, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your category was updated with success');

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/editCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('danger', 'Your category was deleted with success');

        return $this->redirectToRoute('app_category');
    }
}