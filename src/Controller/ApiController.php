<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_a_p_i')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a stream
        $token = "1";
        $opts = [
            "http" => [
                'header' => "endpoint-token: ".$token,
            ]
        ];

        // DOCS: https://www.php.net/manual/en/function.stream-context-create.php
        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        // DOCS: https://www.php.net/manual/en/function.file-get-contents.php
        $user_id = "1";
        $contract_id = "1";
        $file = file_get_contents('http://localhost:8080/api/'.$user_id."/".$contract_id, false, $context);
        return new Response(json_decode($file));
}}
