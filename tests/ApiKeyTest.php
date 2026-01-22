<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiKeyTest extends WebTestCase
{
    private function getUserRepository(): UserRepository
    {
        return static::getContainer()->get(UserRepository::class);
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    private function getPasswordHasher(): UserPasswordHasherInterface
    {
        return static::getContainer()->get(UserPasswordHasherInterface::class);
    }


    private function getJwtToken(string $email = 'test@test.com', string $password = 'test'): string
    {
        $client = static::createClient();
        $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    public function testGenerateApiKey(): void
    {
        // Utiliser l'utilisateur de test existant
        $token = $this->getJwtToken('test@test.com', 'test');

        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('api_key', $data);
        $this->assertArrayHasKey('prefix', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals(64, strlen($data['api_key']));
        $this->assertEquals(16, strlen($data['prefix']));

        // Vérifier que la clé est stockée en base
        $entityManager = $this->getEntityManager();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneBy(['email' => 'test@test.com']);
        if ($user) {
            $entityManager->refresh($user);
            $this->assertNotNull($user->getApiKeyHash());
            $this->assertNotNull($user->getApiKeyPrefix());
            $this->assertTrue($user->isApiKeyEnabled());
        }
    }

    public function testAuthenticateWithValidApiKey(): void
    {
        $token = $this->getJwtToken('test@test.com', 'test');

        // Générer une clé API
        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);
        $apiKey = $data['api_key'];

        // Utiliser la clé API pour accéder à une route API
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        // Vérifier que la date de dernière utilisation est mise à jour
        $entityManager = $this->getEntityManager();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneBy(['email' => 'test@test.com']);
        if ($user) {
            $entityManager->refresh($user);
            $this->assertNotNull($user->getApiKeyLastUsedAt());
        }
    }

    public function testRejectInvalidApiKey(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => 'invalid_api_key_12345',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testRejectDisabledApiKey(): void
    {
        $token = $this->getJwtToken('test@test.com', 'test');

        // Générer une clé API
        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);
        $apiKey = $data['api_key'];

        // Désactiver la clé
        $client = static::createClient();
        $client->request('PATCH', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ],
            'json' => ['enabled' => false]
        ]);

        $this->assertResponseStatusCodeSame(200);

        // Essayer d'utiliser la clé désactivée
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testRegenerateApiKeyInvalidatesOldOne(): void
    {
        $token = $this->getJwtToken('test@test.com', 'test');

        // Générer une première clé API
        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);
        $oldApiKey = $data['api_key'];
        $oldPrefix = $data['prefix'];

        // Vérifier que l'ancienne clé fonctionne
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $oldApiKey,
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);

        // Régénérer une nouvelle clé
        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);
        $newApiKey = $data['api_key'];
        $newPrefix = $data['prefix'];

        // Vérifier que les préfixes sont différents
        $this->assertNotEquals($oldPrefix, $newPrefix);

        // Vérifier que l'ancienne clé ne fonctionne plus
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $oldApiKey,
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);

        // Vérifier que la nouvelle clé fonctionne
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $newApiKey,
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testApiKeyStatus(): void
    {
        $token = $this->getJwtToken('test@test.com', 'test');

        // Vérifier le statut avant génération
        $client = static::createClient();
        $response = $client->request('GET', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['has_api_key']);

        // Générer une clé
        $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        // Vérifier le statut après génération
        $response = $client->request('GET', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['has_api_key']);
        $this->assertNotNull($data['prefix']);
        $this->assertTrue($data['enabled']);
    }

    public function testRevokeApiKey(): void
    {
        $token = $this->getJwtToken('test@test.com', 'test');

        // Générer une clé API
        $client = static::createClient();
        $response = $client->request('POST', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);
        $apiKey = $data['api_key'];

        // Révoquer la clé
        $client = static::createClient();
        $client->request('DELETE', '/api/me/api-key', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        // Vérifier que la clé ne fonctionne plus
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
