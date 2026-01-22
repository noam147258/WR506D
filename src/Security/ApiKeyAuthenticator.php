<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // Check if the request has an API key in the header
        // Only support if no JWT Bearer token is present
        return $request->headers->has('X-API-KEY') &&
               !str_starts_with($request->headers->get('Authorization', ''), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');

        if (null === $apiKey || '' === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        // Extraire le préfixe (16 premiers caractères)
        $prefix = substr($apiKey, 0, 16);

        // Rechercher l'utilisateur par préfixe (indexé, rapide)
        $user = $this->userRepository->findOneBy(['apiKeyPrefix' => $prefix]);

        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        // Vérifier le hash de la clé complète
        $apiKeyHash = hash('sha256', $apiKey);
        if ($user->getApiKeyHash() !== $apiKeyHash) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        if (!$user->isApiKeyEnabled()) {
            throw new CustomUserMessageAuthenticationException('API key is disabled');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), fn () => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Update last used timestamp
        $user = $token->getUser();
        if ($user instanceof \App\Entity\User) {
            $user->updateApiKeyLastUsedAt();
            $this->entityManager->flush();
        }

        // On success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ], Response::HTTP_UNAUTHORIZED);
    }
}
