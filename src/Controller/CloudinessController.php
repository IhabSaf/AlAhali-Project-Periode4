<?php

namespace App\Controller;

use App\Entity\Measurement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CloudinessController extends AbstractController
{
    #[Route('/cloudiness', name: 'app_cloudiness')]
    public function index(EntityManagerInterface $entityManager): Response
    {
      $data = $entityManager->getRepository(Measurement::class)->findBy([], array('timestamp' => 'DESC', 'cldc' => 'DESC'), 10);
        $data_arr = [];
        foreach ($data as $_data ){
            $cldc = $_data->getCldc();
            $station = $_data->getStationName();
            $time_stamp = $_data->getTimestamp()->format('Y-m-d H:i:s');
            $data_arr[] = array(
                0 => $station,
                1 => $cldc,
                2 => $time_stamp
            );
        }
        return $this->render('cloudiness/index.html.twig', [
            'controller_name' => 'CloudinessController',
            'arr' => $data_arr,

        ]);



        //######################################## trying random things to get distinct working

//        $query = $entityManager->createQuery('select DISTINCT station_name, cldc, timestamp
//                                                    from ahli_bank.measurement
//                                                    order by cldc DESC');

    //######################################## trying random things to get distinct working

//        $query = $entityManager->createQueryBuilder();
//        $query->select('station_name', 'cldc', 'timestamp')->distinct()
//                ->from('measurement', "m");
//
//        $nieuwequery = $query->getDQL();
//
////        $buildquery =  $entityManager->createQuery($nieuwequery);
////        $komop = $buildquery->getResult();
        return new Response("<pre>".var_dump($nieuwequery));
    }
}
