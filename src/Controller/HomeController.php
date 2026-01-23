<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // For health check compatibility, return simple JSON if requested by Render
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (str_contains($userAgent, 'Render') || isset($_SERVER['HTTP_X_RENDER'])) {
            return new JsonResponse(['status' => 'ok'], Response::HTTP_OK);
        }
        
        return $this->render('home/index.html.twig');
    }
}
