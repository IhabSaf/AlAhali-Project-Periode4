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


        $qb = $entityManager->createQueryBuilder();
        $qb->select('m.stationName', 'm.longitude', 'm.latitude')
            ->from('App\Entity\Measurement', 'm')
//            ->orderBy('m.stationName','DESC');
            ->setMaxResults(500);
        $query = $qb->getQuery();
        $result = $query->getResult();



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
