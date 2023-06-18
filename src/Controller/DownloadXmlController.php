<?php

namespace App\Controller;

// All imports
use App\Entity\Measurement;
use Spatie\ArrayToXml\ArrayToXml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DownloadXmlController extends AbstractController
{
    #[Route('/download/xml', name: 'download_xml_four_weeks')]
    public function downloadXml(EntityManagerInterface $em) {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $qb = $em->createQueryBuilder();
        $qb
            ->select('m')
            ->from('App\Entity\Measurement', 'm')
        ;
        $query = $qb->getQuery();
        $Results = $query->getArrayResult();

        $xml = ArrayToXml::convert(['Historical_data_Ahli_Bank' => $this->createXMLArray($Results)]);
        $xmlFile = fopen('../templates/four_weeks_xml/four_weeks_data.xml', 'w') or die("File can not be accessed");
        fwrite($xmlFile, $xml);
        fclose($xmlFile);

        return $this->file('../templates/four_weeks_xml/four_weeks_data.xml');
    }

    private function createXMLArray(array $results): array {
        $returnArray = [];
        for($i = 0; $i < count($results); $i++){
            $returnArray["measurement_".$results[$i]["measurementId"]] = [
                'station_name' => $results[$i]["stationName"],
                'timestamp' => substr($results[$i]["timestamp"]->format('Y-m-d H:i:s'), 0, 19),
                'longitude' => $results[$i]["longitude"],
                'latitude' => $results[$i]["latitude"],
                'stp' => $results[$i]["stp"],
                'cldc' => $results[$i]["cldc"]
            ];
        }
        return $returnArray;
    }
}