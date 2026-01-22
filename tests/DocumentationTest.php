<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentationTest extends WebTestCase
{
    public function testApiDocumentationIsAccessible(): void
    {
        // Créer une requête HTTP vers l'URL de la documentation
        $client = static::createClient();
        
        // Vérifiez l'accès à la documentation
        $client->request('GET', '/api/docs');

        // Vérifiez que la réponse a un code 200 (OK)
        $this->assertResponseIsSuccessful();

        // Vérifier que la réponse contient du contenu HTML
        $response = $client->getResponse();
        $this->assertNotEmpty($response->getContent());

        // Vérifier que c'est bien une page HTML (contient des balises HTML)
        $content = $response->getContent();
        $this->assertStringContainsString('html', strtolower($content));
    }
}
