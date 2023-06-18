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
        // hour_now is the year-month-day and hour of right now
        // hour_behind is the year-month-day and hour. The data within 59minute and 59 seconds of the hour_now hour will be given



        $qb = $entityManager->createQueryBuilder();
        $qb->select(' m.stationName','m.cldc', 'm.timestamp')
            ->from('App\Entity\Measurement', 'm')

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
