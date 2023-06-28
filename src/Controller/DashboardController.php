<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
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
        $qb->select('s.stationName', 's.longitude', 's.latitude', 'm.cldc', 'm.stp' )
            ->from('App\Entity\Measurement', 'm')
            ->join('App\Entity\Stations','s', Join::WITH, 's.stationName = m.stationName')
            ->setMaxResults(500);
        $query = $qb->getQuery();
        $result = $query->getResult();
        dump($result);

        //haal alleen maar nu de longitude en latitude.
        $points = [];
        $stationinfo= [];

        foreach ($result as $index => $cord) {
            $longitude = $cord["longitude"];
            $latitude = $cord["latitude"];
            $stationName = $cord["stationName"];
            $cldc =$cord["cldc"];
            $stp = $cord["stp"];
            $points[$index] = [$latitude, $longitude];
            $stationinfo[$index] = [$stationName, $cldc, $stp];
        }
        dump($stationinfo);



        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'points' => $points,
            "station" => $stationinfo
        ]);
    }
}
