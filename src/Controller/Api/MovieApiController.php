<?php

namespace App\Controller\Api;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/movies', name: 'api_movie_')]
class MovieApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(MovieRepository $movieRepository, Request $request): JsonResponse
    {
        // R      cup      rer le param      tre de filtre par titre (optionnel)
        $title = $request->query->get('title');

        // Cr      er la requ      te de base : films online uniquement
        $qb = $movieRepository->createQueryBuilder('m')
            ->where('m.online = :online')
            ->setParameter('online', true);

        // Si un titre est fourni, filtrer par titre
        if ($title) {
            $qb->andWhere('m.name LIKE :title')
               ->setParameter('title', '%' . $title . '%');
        }

        $movies = $qb->orderBy('m.releaseDate', 'DESC') // J'ai mis releaseDate, remets createdAt si tu l'as
                     ->getQuery()
                     ->getResult();

        // Transformer les films en tableau
        $data = [];
        foreach ($movies as $movie) {
            $data[] = [
                'id' => $movie->getId(),
                'name' => $movie->getName(),
                'description' => $movie->getDescription(),
                'duration' => $movie->getDuration(),
                'releaseDate' => $movie->getReleaseDate()?->format('Y-m-d'),
                'online' => $movie->isOnline(),
                'categories' => array_map(fn($c) => $c->getName(), $movie->getCategories()->toArray()),
                'actors' => array_map(
                    fn($a) => $a->getFirstname() . ' ' . $a->getLastname(),
                    $movie->getActors()->toArray()
                ),
            ];
        }

        return $this->json([
            'total' => count($data),
            'movies' => $data
        ]);
    }
}
