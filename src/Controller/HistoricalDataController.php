<?php

namespace App\Controller;

use App\Form\HistoricalStationSelectType;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoricalDataController extends AbstractController
{
    #[Route('/historical/data', name: 'app_historical_data')]
    public function index(Request $request, EntitymanagerInterface $entityManager): Response
    {
        $form = $this->createForm(HistoricalStationSelectType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $today = date('Y-m-d');
            $fourWeeks = 28;
            $dataPerDay = [];
            for($i = 0; $i < $fourWeeks; $i++){
                $qb = $entityManager->createQueryBuilder();
                $qb
                    ->select('avg(m.stp) as stp')
                    ->from('App\Entity\Measurement', 'm')
                    ->where('m.timestamp < :today')
                    ->andWhere('m.timestamp > :yesterday')
                    ->andWhere('m.stationName > :station_name')
                    ->setParameter('today', $today)
                    ->setParameter('yesterday', $this->yesterday($today))
                    ->setParameter('station_name', $data["stationName"])
                    ;
                    $query = $qb->getQuery();
                    $results = $query->getResult();
                    
                    // 死にたい
                    $today = $this->yesterday($today);
                    $dataPerDay[$i] = $results[0];
                }
                
                return $this->render('historical_data/index.html.twig', [
                    'controller_name' => 'HistoricalDataController',
                    'form' => $form->createView(),
                    'selected_station' => $data["stationName"],
                    'formData' => $dataPerDay,
                    'formFilled' => true
                ]);
            }
            
            return $this->render('historical_data/index.html.twig', [
                'controller_name' => 'HistoricalDataController',
                'form' => $form->createView(),
                'selected_station' => 'No station selected',
                'formFilled' => false
            ]);
        }
        
    public function dateFourWeeksAgo(): string{
        $currentTime = date('Y-m-d H:i:s');
        $date = substr($currentTime, 0, 10);
        $time = substr($currentTime, 10);
        $FOURWEEKS = 28;
        
        for($i = 0; $i < $FOURWEEKS; $i++){
            $date = $this->yesterday($date);
        }

        return $date.$time;
    }

    public function yesterday(string $date): string{
        $split = explode('-', $date);
        $year = $split[0]; $month = $split[1]; $day = $split[2];
        $monthDays = [1=>31, 2=>28, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31];
        if(intval($split[0])%4 == 0){
            $monthDays = array_replace($monthDays, array(2 => 29));
        }

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
}
