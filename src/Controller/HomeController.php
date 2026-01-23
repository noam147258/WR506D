<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): JsonResponse
    {
        // Always return simple JSON for health check compatibility
        // Render checks / and needs a fast response without DB dependency
        // This ensures zero-downtime deploys work correctly
        return new JsonResponse([
            'status' => 'ok',
            'timestamp' => time(),
            'service' => 'symfony-app'
        ], Response::HTTP_OK);
    }
}
