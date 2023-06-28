<?php
//php bin/console app:background-data-fetcher
namespace App\Command;

use App\Entity\Measurement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Stations;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class BackgroundDataFetcherCommand extends Command
{
    protected static $defaultName = 'app:background-data-fetcher';
    protected static $defaultDescription = 'Run the background data fetcher';

    private EntityManagerInterface $entityManager;

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

    public function toDatabase($response): void
    {
        // Maak het HTTP-verzoek en haal de inhoud op
        $content = $response->getContent();
        $data = json_decode($content, true);

        // Toegang tot de "WEATHERDATA" in de array
        $weatherData = $data['WEATHERDATA'];

        // Ophalen van station names uit huidige WEATHERDATA | vage constructie voor performance redenen. Als je in de onderste loop steeds in de database gaat kijken of er al een measurement bestaat
        // met de combinatie van stationnaam en timestamp maak je per measurement één doctrine query. Daar wordt het sloom van.
        $measurementStationNames = [];
        foreach ($weatherData as $measurementData) {
            $measurementStationNames[$measurementData['Station_name']] = $measurementData['Date'] . ' ' . $measurementData['Time'];;
        }
//        // doctrine query om combinatie timestamp station name te vinden om later te kunnen kijken welke measurements al eerder zijn verstuurd.
        $stations = $this->entityManager->getRepository(Stations::class)->findBy(array('stationName' => array_keys($measurementStationNames)));
        $alreadyInMeasurementsArray = $this->entityManager->getRepository(Measurement::class)->findBy(array('stationName' => $stations, 'timestamp' => $measurementStationNames));

//        // Zet alle stationnamen in een array. Checken op keys door middel van isset in plaats van in_array met values is een fractie sneller....
        $existingMeasurements = [];
        foreach ($alreadyInMeasurementsArray as $measurement) {
            $existingMeasurements[$measurement->getStationName()->getStationName()] = 0;
        }

        //Loop in de array van measurements. Maak nieuwe measurement aan in database als dat nodig is.
        foreach ($weatherData as $measurementData) {
            $stationName = $measurementData['Station_name'];
            // Bestaat de combinatie stationnaam + measurement timestamp al: niet opnieuw in database zetten.
            if(isset($existingMeasurements[$stationName])) continue;

            $station = $this->entityManager->getRepository(Stations::class)->findOneBy(array('stationName' => $stationName));
            if(!$station) {
                $station = new Stations();
                $station->setStationName((int) $stationName);
                $station->setLatitude($measurementData['Latitude']);
                $station->setLongitude($measurementData['Longitude']);
                $this->entityManager->persist($station);
            }

            // nieuw measurement aanmaken
            $measurement = new Measurement();
            $measurement->setTimestamp(DateTime::createFromFormat('Y:m:d H:i:s', $measurementData['Date'] . ' ' . $measurementData['Time']));
            $measurement->setStationName($station);
            $measurement->setStp(floatval($measurementData['Stp']));
            $measurement->setCldc(floatval($measurementData['Cldc']));
            $this->entityManager->persist($measurement);
        }
        $this->entityManager->flush();
    }
}