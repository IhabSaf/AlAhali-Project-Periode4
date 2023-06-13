<?php

namespace App\Controller;

use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoricalDataController extends AbstractController
{
    #[Route('/historical/data', name: 'app_historical_data')]
    public function index(EntitymanagerInterface $entityManager): Response
    {
        $data = $entityManager->getRepository(Measurement::class)->findBy();
        return $this->render('historical_data/index.html.twig', [
            'controller_name' => 'HistoricalDataController',
        ]);
    }
}
