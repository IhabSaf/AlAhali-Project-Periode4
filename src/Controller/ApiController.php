<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_a_p_i')]
    public function index(): Response
    {
        $token = 1;
        $json = file_get_contents('http://localhost:8000/api/'.$token);
        return new Response($json);
    }
}
