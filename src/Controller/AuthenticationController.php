<?php

namespace App\Controller;

use App\Attribute\MapRequestDTO;
use App\DTO\AuthenticationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    #[Route('/authentication', name: 'app_authentication')]
    public function index(#[MapRequestDTO('a')] AuthenticationDTO $authenticationDTO): JsonResponse
    {
        dd($authenticationDTO);



        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthenticationController.php',
        ]);
    }
}
