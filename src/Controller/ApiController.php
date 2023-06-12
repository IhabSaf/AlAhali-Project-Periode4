<?php

namespace App\Controller;

use App\Entity\Measurement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_a_p_i')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a stream
        $token = "1";
        $opts = [
            "http" => [
                'header' => "endpoint-token: ".$token,
            ]
        ];


        $context = stream_context_create($opts);


        $user_id = "1";
        $contract_id = "1";
        $apiUrl = 'http://localhost:8000/api/'.$user_id.'/'.$contract_id;

        //verwachte aankomst tijd van de data.
        $interval = 5;
        while (true) {
            set_time_limit(60);

            // ophaal de data van de api
            $file = file_get_contents($apiUrl, false, $context);
            $data = json_decode($file, true);

            // tijd van de api en convert meteen
            $timestamp = new DateTime($data['timestamp']);

            foreach ($data as $key => $measurementData) {
                if ($key !== 'timestamp') {
                    $measurement = new Measurement();
                    $measurement->setTimestamp($timestamp);
                    $measurement->setStationName($measurementData[0]);
                    $measurement->setLongitude(2.316);
                    $measurement->setLatitude(96.623);
                    $measurement->setStp($measurementData[1][0]);
                    $measurement->setCldc($measurementData[1][1]);
                    $entityManager->persist($measurement);

                }
            }
            $entityManager->flush();


            // wacht totdat er nieuwe data binnen komt
            sleep($interval);
        }

    }}