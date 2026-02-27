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
    public function index(UserRepository $userRepository): Response  
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    
    #[Route('/admin/user/set_roles/{id}', name:'app_user_set_roles')]
    public function setRoles(EntityManagerInterface $entityManager, User $user): Response
    {

    $user->setRoles(["ROLE_EDITEUR", "ROLE_USER"]);
    $entityManager->flush();
    $this->addFlash('success','La modification a été réalisé avec succès');
    return $this->redirectToRoute('user/index.html.twig');
    }
}
