<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testJwtAuthentication(): void
    {
        $response = static::createClient()->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@test.com',
                'password' => 'test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        
        // Récupérer le token depuis la réponse
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $token = $data['token'];
        
        // Utiliser le token pour accéder à une route API sécurisée
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(200);
    }
    
    public function testApiWithoutToken(): void
    {
        // Tester qu'une route API sans token retourne 401
        $client = static::createClient();
        $client->request('GET', '/api/movies');
        
        $this->assertResponseStatusCodeSame(401);
    }
    
    public function testApiWithInvalidToken(): void
    {
        // Tester qu'une route API avec un token invalide retourne 401
        $client = static::createClient();
        $client->request('GET', '/api/movies', [
            'headers' => [
                'Authorization' => 'Bearer invalid_token_12345',
                'Content-Type' => 'application/json'
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(401);
    }
}
