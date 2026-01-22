<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me/api-key')]
#[IsGranted('ROLE_USER')]
class ApiKeyController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_key_generate', methods: ['POST'])]
    public function generate(UserInterface $user): JsonResponse
    {
        /** @var User $user */
        // Générer la clé API
        $randomBytes = random_bytes(32);
        $apiKey = bin2hex($randomBytes);
        $apiKeyHash = hash('sha256', $apiKey);
        $apiKeyPrefix = substr($apiKey, 0, 16);

        // Stocker le hash et le préfixe
        $user->setApiKeyHash($apiKeyHash);
        $user->setApiKeyPrefix($apiKeyPrefix);
        $user->setApiKeyEnabled(true);
        $user->setApiKeyCreatedAt(new \DateTimeImmutable());
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        // Retourner la clé complète (une seule fois !)
        return new JsonResponse([
            'api_key' => $apiKey,
            'prefix' => $apiKeyPrefix,
            'message' => 'API key generated successfully. Store it securely, it will not be shown again.',
        ], Response::HTTP_CREATED);
    }

    #[Route('', name: 'api_key_status', methods: ['GET'])]
    public function status(UserInterface $user): JsonResponse
    {
        /** @var User $user */
        return new JsonResponse([
            'has_api_key' => $user->getApiKeyHash() !== null,
            'prefix' => $user->getApiKeyPrefix(),
            'enabled' => $user->isApiKeyEnabled(),
            'created_at' => $user->getApiKeyCreatedAt()?->format('Y-m-d H:i:s'),
            'last_used_at' => $user->getApiKeyLastUsedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('', name: 'api_key_toggle', methods: ['PATCH'])]
    public function toggle(UserInterface $user, Request $request): JsonResponse
    {
        /** @var User $user */
        if ($user->getApiKeyHash() === null) {
            return new JsonResponse([
                'error' => 'No API key found. Generate one first.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        $enabled = $data['enabled'] ?? null;

        if ($enabled === null) {
            return new JsonResponse([
                'error' => 'Missing "enabled" field',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->setApiKeyEnabled((bool) $enabled);
        $this->entityManager->flush();

        return new JsonResponse([
            'enabled' => $user->isApiKeyEnabled(),
            'message' => $user->isApiKeyEnabled() ? 'API key enabled' : 'API key disabled',
        ]);
    }

    #[Route('', name: 'api_key_revoke', methods: ['DELETE'])]
    public function revoke(UserInterface $user): JsonResponse
    {
        /** @var User $user */
        $user->setApiKeyHash(null);
        $user->setApiKeyPrefix(null);
        $user->setApiKeyEnabled(false);
        $user->setApiKeyCreatedAt(null);
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'API key revoked successfully',
        ]);
    }
}
