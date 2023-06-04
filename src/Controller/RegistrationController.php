<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $employees = new Employees();

        $form = $this->createForm(RegistrationFormType::class, $employees);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //haal de data van de user input
            $formData = $form->getData();

            //maak de object aan, en sla de gegevens in de database
            $employees->setFirstName($formData->getFirstName());
            $employees->setLastName($formData->getLastName());
            $employees->setEmail($formData->getEmail());
            $employees->setPassword(
                $userPasswordHasher->hashPassword(
                    $employees, $form->get('password')->getData()
                )
            );
            $entityManager->persist($employees);
            $entityManager->flush();


        }
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'form' => $form->createView(),
        ]);
    }
}
