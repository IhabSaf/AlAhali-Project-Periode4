<?php
//php bin/console app:background-data-fetcher
namespace App\Command;

use App\Entity\Measurement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

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
        $apiUrl = 'http://localhost:8010/api/'.$user_id.'/'.$contract_id;

        // Expected arrival time of the data.
        $interval = 5;
        while (true) {
            set_time_limit(60);

            // Retrieve data from the API
            $file = file_get_contents($apiUrl, false, $context);
            $data = json_decode($file, true);

            // Time of the API and convert immediately
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
                    $this->entityManager->persist($measurement);
                }
            }

            $this->entityManager->flush();

            // Wait until new data arrives
            sleep($interval);
        }

        return Command::SUCCESS;
    }
}
