<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $repo): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_user_edit')]
    public function editUser(User $user, EntityManagerInterface $entityManager): Response
    {   
        $userRole = $user->getRoles();

        if (in_array('ROLE_EDITOR', $userRole)) {
            $user->setRoles( array('ROLE_USER') );
            $entityManager->flush();
            $this->addFlash('danger', 'The user is not an editor anymore');
        } else {
            $user->setRoles( array('ROLE_EDITOR', 'ROLE_USER') );
            $entityManager->flush();
            $this->addFlash('success', 'The user is now an editor');
        }

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', 'The user was deleted with success');

        return $this->redirectToRoute('app_user');
    }
}