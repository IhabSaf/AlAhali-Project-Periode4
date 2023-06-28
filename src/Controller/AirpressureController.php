<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AirpressureController extends AbstractController
{
    #[Route('/airpressure', name: 'app_airpressure')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $hour_now = date('Y-m-d H:');
        $hour_now = $hour_now.'00:00';
        $hour_behind = date('Y-m-d H:');
        $hour_behind = $hour_behind.'59:59';
        // query to get the high airpressure
        $qb = $entityManager->createQueryBuilder();
        $qb->select('s.stationName', 'm.stp', 'm.timestamp')
            ->from('App\Entity\Measurement', 'm')
            ->join('App\Entity\Stations','s', Join::WITH, 's.stationName = m.stationName')
            ->where('m.stp > 1029')
//            ->andWhere('m.stp != 0')
            ->orWhere('m.stp < 990')
//            ->andWhere('m.stp != 0')
            ->orderBy('m.timestamp', 'DESC')
            ->setMaxResults(20);
        $query = $qb->getQuery();
        $result_stp = $query->getResult();



        return $this->render('airpressure/index.html.twig', [
            'controller_name' => 'AirpressureController',
            'Stp' => $result_stp
        ]);
    }
}
