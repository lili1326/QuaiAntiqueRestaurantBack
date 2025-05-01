<?php
namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class SmokeTest extends WebTestCase
{
    public function testApiDocUrlIsSuccessful(): void
    {
        //On crée un client HTTP fictif pour tester ton site
        $client = self::createClient();
        //On désactive le suivi automatique des redirections
        //pour vérifier si /api/doc renvoie bien un bon statut sans être redirigé.
        $client->followRedirects(false);
        //Le client envoie une requête GET vers l’URL /api/doc.
        $client->request('GET', '/api/doc');
        //On vérifie que la réponse a un code HTTP dans la plage 200-299, ce qui signifie succès.
        self::assertResponseIsSuccessful();
    }

    public function testApiAccountUrlIsSuccessful(): void
    {
        // si on n’est pas connecté, on doit recevoir un code 401.
        $client = self::createClient();
        $client->followRedirects(false);  
        $client->request('GET', '/api/account/me');  
        self::assertResponseStatusCodeSame(401);
    }

     
}