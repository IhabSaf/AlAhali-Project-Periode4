<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AirpressureController extends AbstractController
{
    #[Route('/airpressure', name: 'app_airpressure')]
    public function index(): Response
    {
        return $this->render('airpressure/index.html.twig', [
            'controller_name' => 'AirpressureController',
        ]);
    }
}
