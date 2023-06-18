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

    #[Route('/historical/data/dowload', name: 'app_historical_data_download')]
    public function download(){
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->file('../templates/historical_data/historical_data.xml');
    }

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
                $qb
                    ->select('avg(m.stp) as stp')
                    ->from('App\Entity\Measurement', 'm')
                    ->where('m.timestamp < :today')
                    ->andWhere('m.timestamp > :yesterday')
                    ->andWhere('m.stationName = :station_name')
                    ->andWhere('m.stp < 990')
                    ->setParameter('today', $today." 00:00:00")
                    ->setParameter('yesterday', $this->yesterday($today)." 00:00:00")
                    ->setParameter('station_name', $data["stationName"])
                ;
                $query = $qb->getQuery();
                $lowResults = $query->getArrayResult();

                // Dataset above 1030mBar | Average of a single day
                $qb = $entityManager->createQueryBuilder();
                $qb
                    ->select('avg(m.stp) as stp')
                    ->from('App\Entity\Measurement', 'm')
                    ->where('m.timestamp < :today')
                    ->andWhere('m.timestamp > :yesterday')
                    ->andWhere('m.stationName = :station_name')
                    ->andWhere('m.stp > 1030')
                    ->setParameter('today', $today." 00:00:00")
                    ->setParameter('yesterday', $this->yesterday($today)." 00:00:00")
                    ->setParameter('station_name', $data["stationName"])
                ;
                $query = $qb->getQuery();
                $highResults = $query->getArrayResult();
                
                // Put all data and dates in arrays to be sent to the template
                $lowDataPerDay[$i] = $lowResults[0];
                $highDataPerDay[$i] = $highResults[0];
                $today = $this->yesterday($today);
                $dates[$i] = $this->dayMonthFormat($today);
                $split = explode('-', $today);
                $days[$i] = $split[2];
                $months[$i] = $split[1];
                }
                
                $xml = ArrayToXml::convert($this->createXMLArray("name".$data["stationName"], array_reverse($dates), $lowDataPerDay, $highDataPerDay), 'Historical_data_Ahli_Bank');
                $xmlFile = fopen('../templates/historical_data/historical_data.xml', 'w') or die("File can not be accessed");
                fwrite($xmlFile, $xml);
                fclose($xmlFile);

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
                    'formMonths' => array_reverse($months)
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

        return strval($year).'-'.strval($month).'-'.strval($day);
    }

    public function dayMonthFormat(string $date): string {
        $split = explode('-', $date);
        return $split[2].'-'.$split[1];
    }

    public function createXMLArray(string $stationName, array $dates, array $lowDataPerDay, array $highDataPerDay): array{
        $blocks = [];
        $fourWeeks = 28;
        for($i = 0; $i < $fourWeeks; $i++){
            $blocks["date_".$dates[$i]] = [
                'lowStp' => $lowDataPerDay[$i]['stp'],
                'highStp' => $highDataPerDay[$i]['stp']
            ];
        }
        return $xmlArray = [$stationName => $blocks];
    }
}
