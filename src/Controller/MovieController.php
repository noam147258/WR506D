<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movies', name: 'app_movie_list')]
    public function list(
        MovieRepository $movieRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupérer tous les films
        $query = $movieRepository->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();
        
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page (1 par défaut)
            10 // Nombre d'éléments par page
        );
        
        return $this->render('movie/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
