<?php

namespace App\Controller;

// All imports
use DateTime;
use Spatie\ArrayToXml\ArrayToXml;
use App\Form\HistoricalStationSelectType;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class HistoricalDataController extends AbstractController
{
    /**
     * Render a page with form to ask for a station_name.
     * When the form is filled in, a chart will be shown with average
     * stp data per day, up to 4 weeks prior to the current day.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response render a template
     */
    #[Route('/historical/data', name: 'app_historical_data')]
    public function index(Request $request, EntitymanagerInterface $entityManager): Response
    {
        // Als de user niet ingelogd dan wordt hij verwijst weer naar de inlog pagina
        if (!$this->getUser()) {return $this->redirectToRoute('app_login');}

        // Create form to get station_name
        $form = $this->createForm(HistoricalStationSelectType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            // Collect all average stp's in an array
            $today = new DateTime();
            $today->modify('+1 day');
            $today = $today->format('Y-m-d');
            $fourWeeks = 28;
            $dataPerDay = [];
            $days = [];
            for($i = 0; $i < $fourWeeks; $i++){
                // Dataset below 990mBar | Average of a single day
                $qb = $entityManager->createQueryBuilder();
                $qb->select('avg(m.stp) as stp')
                    ->from('App\Entity\Measurement', 'm')
                    ->where('m.timestamp < :today')
                    ->andWhere('m.timestamp > :yesterday')
                    ->andWhere('m.stationName = :station_name')
                    ->andWhere('m.stp < 991')
                    ->setParameter('today', $today." 00:00:00")
                    ->setParameter('yesterday', $this->yesterday($today)." 00:00:00")
                    ->setParameter('station_name', $data["stationName"])
                ;
                $lowResults = $qb->getQuery()->getResult();

                // Dataset above 1030mBar | Average of a single day
                $qb = $entityManager->createQueryBuilder();
                $qb->select('avg(m.stp) as stp')
                    ->from('App\Entity\Measurement', 'm')
                    ->where('m.timestamp < :today')
                    ->andWhere('m.timestamp > :yesterday')
                    ->andWhere('m.stationName = :station_name')
                    ->andWhere('m.stp > 1029')
                    ->setParameter('today', $today." 00:00:00")
                    ->setParameter('yesterday', $this->yesterday($today)." 00:00:00")
                    ->setParameter('station_name', $data["stationName"])
                ;
                $highResults = $qb->getQuery()->getResult();

                // Put all data and dates in arrays to be sent to the template
                $lowDataPerDay[$i] = $this->roundMeasurement($lowResults);
                $highDataPerDay[$i] = $this->roundMeasurement($highResults);
                $today = $this->yesterday($today);
                $dates[$i] = $this->dayMonthFormat($today);
                $split = explode('-', $today);
                $days[$i] = $split[2];
                $months[$i] = $split[1];
            }

            // Render page with data
            return $this->render('historical_data/index.html.twig', [
                'controller_name' => 'HistoricalDataController',
                'form' => $form->createView(),
                'selected_station' => $data["stationName"],
                'formFilled' => true,
                // All arrays are reversed so the data is shown left to right according to date
                'lowFormData' => array_reverse($lowDataPerDay),
                'highFormData' => array_reverse($highDataPerDay),
                'formDays' => array_reverse($days),
                'formMonths' => array_reverse($months),
                'formDates' => array_reverse($dates)
            ]);
        }
            
            // Render page without data
            return $this->render('historical_data/index.html.twig', [
                'controller_name' => 'HistoricalDataController',
                'form' => $form->createView(),
                'selected_station' => 'No station selected',
                'formFilled' => false
            ]
        );
    }

    private function roundMeasurement($measurement): array
    {
        $floatValue = $measurement[0];
        if($floatValue['stp'] != null) {
            $roundedVal = round($floatValue['stp'], 1);
        } else {
            $roundedVal = null;
        }
        $arr['stp'] = $roundedVal;
        return $arr;
    }

    /**
     * Input a date in (Y-m-d) format and returns a date a day before input.
     * @param string $date (Y-m-d)
     * @return string $date (Y-m-d)
     */
    public function yesterday(string $date): string{
        $split = explode('-', $date); // Split date to array
        $year = $split[0]; $month = $split[1]; $day = $split[2];
        $monthDays = [1=>31, 2=>28, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31]; // Days per month

        // Leap year -> February has 29 days
        if(intval($split[0])%4 == 0){$monthDays = array_replace($monthDays, array(2 => 29));}

        // If day/month falls below 1, update previous [year,month,day]
        $day = intval($split[2]) - 1;
        if ($day <= 0){
            $month = intval($month) - 1;
            $day = ($month >= 1 ? $monthDays[$month] : $monthDays[12]);
        }
        if ($month <= 0){
            $year = intval($year) -1;
            $month = 12;
        }
        if($day < 10) $day = '0'.intval($day);
        if($month < 10) $month = '0'.intval($month);

        return $year .'-'. $month .'-'. $day;
    }

    public function dayMonthFormat(string $date): string {
        $split = explode('-', $date);
        return $split[2].'-'.$split[1];
    }


    // NOTE vanuit hier en beneden dit is gemaakt voor de map als je op VIEW HISTORICAL DATA VANUIT DE MAP DAN WORDT DE ONDERSTAANDE CODE GETRIGERD.
    // ER ZIJN AFHANKELIJKHEDEN VAN WAT ONDERAAN STAAT EN WAT AALDERT HEEFT GEMAAKT, DUS BIJ AANPASSINGEN HOU EVEN REKEKING MEE.


    #[Route('/historical/data/1', name: 'app_historical_data_map', methods: ['POST', 'GET'])]
    public function map(Request $request, EntitymanagerInterface $entityManager):Response
    {
        // Retrieve the station ID from the request
        $station = trim($request->request->get('stationId'), ' " ');

        // Collect all average stp's in an array
        $today = new DateTime();
        $today->modify('+1 day');
        $today = $today->format('Y-m-d');
        $fourWeeks = 28;
        $dataPerDay = [];
        $days = [];
        for ($i = 0; $i < $fourWeeks; $i++) {
            // Dataset below 990mBar | Average of a single day
            $qb = $entityManager->createQueryBuilder();
            $qb
                ->select('avg(m.stp) as stp')
                ->from('App\Entity\Measurement', 'm')
                ->where('m.timestamp < :today')
                ->andWhere('m.timestamp > :yesterday')
                ->andWhere('m.stationName = :station_name')
                ->andWhere('m.stp < 991')
                ->setParameter('today', $today . " 00:00:00")
                ->setParameter('yesterday', $this->yesterday($today) . " 00:00:00")
                ->setParameter('station_name', $station);
            $query = $qb->getQuery();
            $lowResults = $query->getResult();

            // Dataset above 1030mBar | Average of a single day
            $qb = $entityManager->createQueryBuilder();
            $qb
                ->select('avg(m.stp) as stp')
                ->from('App\Entity\Measurement', 'm')
                ->where('m.timestamp < :today')
                ->andWhere('m.timestamp > :yesterday')
                ->andWhere('m.stationName = :station_name')
                ->andWhere('m.stp > 1029')
                ->setParameter('today', $today . " 00:00:00")
                ->setParameter('yesterday', $this->yesterday($today) . " 00:00:00")
                ->setParameter('station_name', $station);
            $query = $qb->getQuery();
            $highResults = $query->getResult();


            // Put all data and dates in arrays to be sent to the template
            $lowDataPerDay[$i] = $this->roundMeasurement($lowResults);
            $highDataPerDay[$i] = $this->roundMeasurement($highResults);
            $today = $this->yesterday($today);
            $dates[$i] = $this->dayMonthFormat($today);
            $split = explode('-', $today);
            $days[$i] = $split[2];
            $months[$i] = $split[1];
        }

        return $this->render('historical_data/chartPerPointer.html.twig', [
            'controller_name' => 'HistoricalDataController',
            'selected_station' => $station,
            'formFilled' => true,
            'lowFormData' => array_reverse($lowDataPerDay),
            'highFormData' => array_reverse($highDataPerDay),
            'formDays' => array_reverse($days),
            'formMonths' => array_reverse($months),
            'formDates' => array_reverse($dates)
        ]);
    }

}

