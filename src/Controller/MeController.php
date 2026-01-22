<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MeController
{
    #[Route("/api/me", name: "get_current_user", methods: ["GET"])]
    public function getCurrentUser(UserInterface $user): JsonResponse
    {
        /** @var User $user */
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            // etc....
        ];

        return new JsonResponse($userData);
    }
}
