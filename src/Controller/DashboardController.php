<?php

namespace App\Controller;

use App\Entity\Measurement;
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

        //Haal per station de meest recente timestamp op. Hiermee kan in de volgende query de rest van de waardes worden opgehaald.
        $repository = $entityManager->getRepository(Measurement::class);
        $qb = $repository->createQueryBuilder('m');
        $qb->select('s.stationName', 'MAX(m.timestamp) as maxTimestamp')
            ->innerJoin('App\Entity\Stations', 's', Join::WITH, 's.stationName = m.stationName')
            ->groupBy('s.stationName');
        $query = $qb->getQuery();
        $result = $query->getResult();

        //Haal nu de bijbehorende data op voor de meest recente measurement per station | Inclusief longitude en latitude voor performance
        $qb2 = $repository->createQueryBuilder('m');
        $qb2->select('s.stationName, m.timestamp, m.stp, m.cldc, s.longitude, s.latitude');
        $qb2->innerJoin('App\Entity\Stations', 's', Join::WITH, 's.stationName = m.stationName');

        //lange where and constructie maken om de juiste measurements op te halen.
        $whereCondition = [];
        foreach($result as $record) {
            $whereCondition[] = sprintf("m.stationName = '%s' AND m.timestamp = '%s'", $record['stationName'], $record['maxTimestamp']);
        }
        $whereClause = implode(' OR ', $whereCondition);
        $qb2->where($whereClause);
        $measurements = $qb2->getQuery()->getResult();

        $points = [];
        $stationInfo= [];
        foreach ($measurements as $index => $measurement) {
            $longitude = $measurement['longitude'];
            $latitude = $measurement['latitude'];
            $stationName = $measurement['stationName'];
            $cldc = $measurement['cldc'];
            $stp = $measurement['stp'];
            $points[$index] = [$latitude, $longitude];
            $stationInfo[$index] = [$stationName, $cldc, $stp];
        }
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'points' => $points,
            "station" => $stationInfo
        ]);
    }
}
