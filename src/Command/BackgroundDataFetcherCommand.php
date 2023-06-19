<?php

//php bin/console app:background-data-fetcher
namespace App\Command;

use App\Entity\Measurement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class BackgroundDataFetcherCommand extends Command
{
    protected static $defaultName = 'app:background-data-fetcher';
    protected static $defaultDescription = 'Run the background data fetcher';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // Op het moment dat de script aan is, dan wordt dit while loop getrigerd om de data steeds vanuit de IWA op te halen.
        while(true){
            set_time_limit(60);
            $httpClient = HttpClient::create();
            $headers = [
                'api-key' => 'I8h5fxj1B61CbbN6xBkLFWLDlsmZMGY7nFpMIkKK54XjS1RqjzYwlcZ1noqwVMFE0xeoctqmH4u9JLLXQUlEnV8oQ7m3obpwoTH0',
            ];
            $response = $httpClient->request('GET', 'http://localhost:8020/api/contract/5', [
                'headers' => $headers,
            ]);
            $this->toDatabase($response);
            sleep(5);
        }

    }

    public function toDatabase($response)
    {
        // Maak het HTTP-verzoek en haal de inhoud op
        $content = $response->getContent();
        $data = json_decode($content, true);

        // Toegang tot de "WEATHERDATA" in de array
        $weatherData = $data['WEATHERDATA'];

        // Ophalen van station names uit huidige WEATHERDATA | vage constructie voor performance redenen. Als je in de onderste loop steeds in de database gaat kijken maak je per measurement één doctrine query. Daar wordt het sloom van.
        $arrMesmStnameTime = [];
        foreach ($weatherData as $measurementData) {
            $timestampString = $measurementData['Date'] . ' ' . $measurementData['Time'];
            $arrMesmStnameTime[$measurementData['Station_name']] = $timestampString;
        }

        // één doctrine query om combinatie timestamp station name te vinden om later te kunnen kijken welke measurements al eerder zijn verstuurd.
        $alreadyInMeasurementsArray = $this->entityManager->getRepository(Measurement::class)->findBy(array('stationName' => array_keys($arrMesmStnameTime), 'timestamp' => $arrMesmStnameTime));

        // Zet alle station names in een array. Checken op keys in plaats van values is extreem klein beetje sneller....
        $existingMeasurements = [];
        foreach ($alreadyInMeasurementsArray as $measurement) {
            $existingMeasurements[$measurement->getStationName()] = 0;
        }

        //loop in de array en haal de data
        foreach ($weatherData as $measurementData) {
            $stationName = $measurementData['Station_name'];
            // Bestaat de combinatie stationname + measurement timestamp al: niet opnieuw in database zetten.
            if(isset($existingMeasurements[$stationName])) continue;

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
            $this->entityManager->persist($measurement);
            $this->entityManager->flush();
        }
    }
}