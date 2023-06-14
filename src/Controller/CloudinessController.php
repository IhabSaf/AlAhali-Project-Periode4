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
//    #[Route('/cloudiness', name: 'app_cloudiness')]
//    public function index(EntityManagerInterface $entityManager): Response
//    {
//
//        $data = $entityManager->getRepository(Measurement::class)->findBy([], array('timestamp' => 'DESC', 'cldc' => 'DESC'), 10);
//        $data->
//        $data_arr = [];
//        foreach ($data as $_data ){
//            $cldc = $_data->getCldc();
//            $station = $_data->getStationName();
//            $time_stamp = $_data->getTimestamp()->format('Y-m-d H:i:s');
//            $data_arr[] = array(
//                0 => $station,
//                1 => $cldc,
//                2 => $time_stamp
//            );
//        }
//        return $this->render('cloudiness/index.html.twig', [
//            'controller_name' => 'CloudinessController',
//            'arr' => $data_arr,
//
//        ]);
// And !in_array($filter_data->getStationName(), $data_arr, true)
    #[Route('/cloudiness', name: 'app_cloudiness')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // hour_now is the year-month-day and hour of right now
        // hour_behind is the year-month-day and hour. The data within 59minute and 59 seconds of the hour_now hour will be given

        $hour_now = date('Y-m-d H:');
        $hour_now = $hour_now.'00:00';
        $hour_behind = date('Y-m-d H:');
        $hour_behind = $hour_behind.'59:59';

        $qb = $entityManager->createQueryBuilder();
        $qb->select(' m.stationName','m.cldc', 'm.timestamp')
            ->from('App\Entity\Measurement', 'm')
            ->where('m.timestamp > :hour_now')
            ->andWhere('m.timestamp < :hour_behind')
            ->setParameter('hour_now',$hour_now)
            ->setParameter('hour_behind',$hour_behind )
            ->orderBy('m.cldc','DESC')
            ->setMaxResults(10);
      $query = $qb->getQuery();
      $result = $query->getResult();


        return $this->render('cloudiness/index.html.twig', [
            'controller_name' => 'CloudinessController',
            'Results' => $result,

        ]);
    }
}
