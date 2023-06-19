<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Als de user niet ingelogd dan wordt hij verwijst weer naar de inlog pagina
        if (!$this->getUser()) {return $this->redirectToRoute('app_login');}


        // Haal de langitude en latitude vanuit de database.
        $qb = $entityManager->createQueryBuilder();
        $qb->select('m.stationName', 'm.longitude', 'm.latitude')
            ->from('App\Entity\Measurement', 'm')
            ->setMaxResults(500);
        $query = $qb->getQuery();
        $result = $query->getResult();

        //haal alleen maar nu de longitude en latitude.
        $points = [];
        foreach ($result as $index => $cord) {
            $longitude = $cord["longitude"];
            $latitude = $cord["latitude"];
            $points[$index] = [$latitude, $longitude];
        }


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'points' => $points
        ]);
    }
}
