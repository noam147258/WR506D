<?php

namespace App\Controller;

use App\Repository\ActorRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ActorController extends AbstractController
{
    #[Route('/actors', name: 'app_actor_list')]
    public function list(
        ActorRepository $actorRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupérer tous les acteurs
        $query = $actorRepository->createQueryBuilder('a')
            ->orderBy('a.lastname', 'ASC')
            ->getQuery();
        
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page (1 par défaut)
            10 // Nombre d'éléments par page
        );
        
        return $this->render('actor/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
