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
        // For health check compatibility (Render checks /)
        // Return simple JSON response that doesn't require database
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $userAgent = $request?->headers->get('User-Agent', '') ?? '';
        
        // If it's a health check (Render or simple GET without Accept: text/html)
        $accept = $request?->headers->get('Accept', '') ?? '';
        if (str_contains($userAgent, 'Render') || 
            str_contains($userAgent, 'HealthCheck') ||
            (!str_contains($accept, 'text/html') && $request?->getMethod() === 'GET')) {
            return new JsonResponse(['status' => 'ok', 'timestamp' => time()], Response::HTTP_OK);
        }
        
        // Normal request - render template (may require DB, but health check won't reach here)
        try {
            return $this->render('home/index.html.twig');
        } catch (\Exception $e) {
            // If template rendering fails (e.g., DB not ready), return simple response
            return new JsonResponse(['status' => 'ok', 'message' => 'Application starting'], Response::HTTP_OK);
        }
    }
}
