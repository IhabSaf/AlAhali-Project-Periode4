<?php

namespace App\Controller;

use App\Entity\Measurement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class CloudinessController extends AbstractController
{

    #[Route('/cloudiness', name: 'app_cloudiness')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Als de user niet ingelogd dan wordt hij verwijst weer naar de inlog pagina
        if (!$this->getUser()) {return $this->redirectToRoute('app_login');}

        // hour_now is the year-month-day and hour of right now
        // hour_behind is the year-month-day and hour. The data within 59minute and 59 seconds of the hour_now hour will be given
        $hour_now = date('Y-m-d H:');
        $hour_now = $hour_now.'00:00';
        $hour_behind = date('Y-m-d H:');
        $hour_behind = $hour_behind.'59:59';

        $qb = $entityManager->createQueryBuilder();
        $qb->select(' s.stationName','m.cldc', 'm.timestamp')
            ->from('App\Entity\Measurement', 'm')
            ->join('App\Entity\Stations','s', Join::WITH, 's.stationName = m.stationName')
            ->where('m.timestamp > :hour_now')
            ->andWhere('m.timestamp < :hour_behind')
            ->setParameter('hour_now',$hour_now)
            ->setParameter('hour_behind',$hour_behind )
            ->orderBy('m.cldc','DESC')
            ->setMaxResults(100);
      $query = $qb->getQuery();
      $result = $query->getResult();


        // Filter de duplicate van station names
        $filteredResult = [];
        foreach ($result as $data) {
            $stationName = $data['stationName'];
            if (!isset($filteredResult[$stationName])) {
                $filteredResult[$stationName] = $data;
            }
        }

        // makes sure that there is always 10 values
        $filteredResult = array_slice($filteredResult, 0, 10);

        return $this->render('cloudiness/index.html.twig', [
            'controller_name' => 'CloudinessController',
            'filteredResult' => $filteredResult,


        ]);
    }
}
