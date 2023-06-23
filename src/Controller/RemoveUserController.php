<?php

namespace App\Controller;

use App\Entity\Employees;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RemoveUserController extends AbstractController
{
    #[Route('/remove/user', name: 'app_remove_user')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // haal alle user vna de database.
        $userRepository = $entityManager->getRepository(Employees::class);
        $users = $userRepository->findAll();

        return $this->render('remove_user/index.html.twig', [
            'controller_name' => 'RemoveUserController',
            'users' => $users,
        ]);
    }

    #[Route('/remove/user/{id}', name: 'app_remove_user_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(Employees::class);
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_remove_user');
    }

    #[Route('/remove/user/{id}/change-email', name: 'app_remove_user_change_email', methods: ['POST'])]
    public function changeEmail(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(Employees::class);
        $user = $userRepository->find($id);


        $newEmail = $request->request->get('email');
        $user->setEmail($newEmail);
        $entityManager->flush();


        return $this->redirectToRoute('app_remove_user');
    }

    #[Route('/remove/user/{id}/change-password', name: 'app_remove_user_change_password', methods: ['POST'])]
    public function changePassword(int $id, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        //zoek de betreffende gebruiker.
        $userRepository = $entityManager->getRepository(Employees::class);
        $user = $userRepository->find($id);


        $newPassword = $request->request->get('password');

        // Validate en update het nieuwe wachtwoord.
        if ($newPassword) {
            $hashedPassword = $passwordEncoder->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            // Redirect to the remove user page
            return $this->redirectToRoute('app_remove_user');
        }
        return $this->redirectToRoute('app_remove_user');

    }
}
