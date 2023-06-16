<?php

namespace App\Controller;

use App\Entity\Measurement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

        $day_start = date('Y-m-d H:');
        $day_start = $day_start.'00:00';
        $end_day = date('Y-m-d H:');
        $end_day = $end_day.'59:59';

        $qb = $entityManager->createQueryBuilder();
        $qb->select(' m.stationName','m.cldc', 'm.timestamp')
            ->from('App\Entity\Measurement', 'm')
            ->where('m.timestamp > :day_start')
            ->andWhere('m.timestamp < :end_day')
            ->setParameter('day_start',$day_start)
            ->setParameter('end_day',$end_day )
            ->orderBy('m.cldc','DESC')
            ->setMaxResults(10);
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

        return $this->render('cloudiness/index.html.twig', [
            'controller_name' => 'CloudinessController',
            'filteredResult' => $filteredResult,


        ]);
    }
}
