<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Form\RemoveUserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class RemoveUserController extends AbstractController
{
    #[Route('/remove/user', name: 'app_remove_user')]
    public function index(Request $request, EntitymanagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(RemoveUserType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            $userRepository = $entityManager->getRepository(Employees::class);
            $user = $userRepository->findOneBy(['email' => $formData['email']]);
            if ($user) {
                $entityManager->remove($user);
                $entityManager->flush();

            } else {
                throw new \Exception("User not found");
            }


        }

        return $this->render('remove_user/index.html.twig', [
            'controller_name' => 'RemoveUserController',
            'form' => $form->createView(),
        ]);
    }
}
