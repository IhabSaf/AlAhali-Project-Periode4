<?php

namespace App\Controller;

// All imports
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DownloadXmlController extends AbstractController
{
    #[Route('/download/xml', name: 'download_xml_four_weeks')]
    public function downloadXml(EntityManagerInterface $em): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {

        // Als de user niet ingelogd dan wordt hij verwijst weer naar de inlog pagina
        if (!$this->getUser()) {return $this->redirectToRoute('app_login');}

        $results = $em->getRepository(Measurement::class)->findAll();

        $writer = xmlwriter_open_uri('../templates/four_weeks_xml/four_weeks_data.xml');
        xmlwriter_set_indent($writer, true);
        xmlwriter_set_indent_string($writer, '  ');

        xmlwriter_start_document($writer, '1.0', 'UTF-8');
        xmlwriter_start_element($writer, 'root');

        foreach($results as $measurement) {
            $this->createMeasurementElement($writer, $measurement);
        }

        xmlwriter_end_element($writer);
        xmlwriter_end_document($writer);

        xmlwriter_flush($writer);

        return $this->file('../templates/four_weeks_xml/four_weeks_data.xml');
    }

    private function createMeasurementElement($writer, $measurement):void {
        xmlwriter_start_element($writer, 'measurement');

        xmlwriter_start_attribute($writer, 'measurement_id');
        xmlwriter_text($writer,  $measurement->getMeasurementId());
        xmlwriter_end_attribute($writer);

        xmlwriter_start_element($writer, 'station_name');
        xmlwriter_text($writer,  $measurement->getStationName()->getStationName());
        xmlwriter_end_element($writer);

        xmlwriter_start_element($writer, 'timestamp');
        xmlwriter_text($writer,  $measurement->getTimestamp()->format('Y-m-d H:i:s'));
        xmlwriter_end_element($writer);

        xmlwriter_start_element($writer, 'stp');
        xmlwriter_text($writer, $measurement->getStp());
        xmlwriter_end_element($writer);

        xmlwriter_start_element($writer, 'cldc');
        xmlwriter_text($writer, $measurement->getCldc());
        xmlwriter_end_element($writer);

        xmlwriter_end_element($writer);
    }
}