<?php

namespace App\Controller;

use App\Entity\Measurement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/cloudiness', name: 'app_cloudiness')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $data_arr = [];
        $full_data_arr = $this->filterData($entityManager, $data_arr);
        $today = date("Y-m-d");
//        return new Response("<pre>". var_dump($full_data_arr));
        return $this->render('cloudiness/index.html.twig', [
            'controller_name' => 'CloudinessController',
            'arr' => $full_data_arr,

        ]);


    }
    public function filterData(EntityManagerInterface $entityManager, array $data_arr): array
    {
        $today = date("Y-m-d");
        $data = $entityManager->getRepository(Measurement::class)->findBy([], array('cldc' => 'DESC', 'timestamp' => 'DESC'), 1000);
        foreach ($data as $filter_data) {
            if ($filter_data->getTimestamp()->format('Y-m-d') == $today ) { // && !data_arr($filter_data->getStationName()) 
                $cldc = $filter_data->getCldc();
                $station = $filter_data->getStationName();
                $time_stamp = $filter_data->getTimestamp()->format('Y-m-d H:i:s');
                $data_arr[] = array(
                    0 => $station,
                    1 => $cldc,
                    2 => $time_stamp
                );
            }
        }
        return $data_arr;
    }
}
