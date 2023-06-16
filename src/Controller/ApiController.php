<?php

namespace App\Controller;

use App\Entity\Measurement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_a_p_i')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {

        // op het moment dat de script aan is, dan wordt dit while loop getrigerd om de data steeds vanuit de IWA op te halen.
        while(true){
            set_time_limit(60);
            $httpClient = HttpClient::create();
            $headers = [
                'api-key' => 'I8h5fxj1B61CbbN6xBkLFWLDlsmZMGY7nFpMIkKK54XjS1RqjzYwlcZ1noqwVMFE0xeoctqmH4u9JLLXQUlEnV8oQ7m3obpwoTH0',
            ];
            $response = $httpClient->request('GET', 'http://localhost:8020/api/contract/5', [
                'headers' => $headers,
            ]);
            $this->toDatabase($response, $entityManager);
            sleep(5);
        }

    }

    public function toDatabase($response, $entityManager)
    {
        // Maak het HTTP-verzoek en haal de inhoud op
        $content = $response->getContent();
        $data = json_decode($content, true);

        // Toegang tot de "WEATHERDATA" in de array
        $weatherData = $data['WEATHERDATA'];

        //loop in de array en haal de data
        foreach ($weatherData as $measurementData) {
            // Maak de tijd formaat
            $timestampString = $measurementData['Date'] . ' ' . $measurementData['Time'];
            $timestamp = DateTime::createFromFormat('Y:m:d H:i:s', $timestampString);

            // nieuwe object en dan in de database opslaan.
            $measurement = new Measurement();
            $measurement->setTimestamp($timestamp);
            $measurement->setStationName($measurementData['Station_name']);
            $measurement->setLongitude($measurementData['Longitude']);
            $measurement->setLatitude($measurementData['Latitude']);
            $measurement->setStp(floatval($measurementData['Stp']));
            $measurement->setCldc(floatval($measurementData['Cldc']));
            $entityManager->persist($measurement);
            $entityManager->flush();
        }
    }
}