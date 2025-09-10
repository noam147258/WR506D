<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MyEventService;

class DemoController extends AbstractController
{
    #[Route('/demo', name: 'app_demo')]
    public function index(MyEventService $service): Response
    {
        $service->doSomething(); // Déclenche l’événement

        return new Response("Événement déclenché, regarde les logs/debug 😎");
    }
}
