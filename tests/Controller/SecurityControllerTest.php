<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoadsForAnonymousUser(): void
    {   // 1; créé un client HTTP simulé
        $client = static::createClient();
       
        // on effectue une requete GET vers l url '/login'
       $crawler = $client->request('GET', '/login');
        // On verifie que la reponse est 200 OK
        $this->assertResponseIsSuccessful();

       // $this->assertResponseStatusCodeSame(200);
        // On verifie que le formulaire de connexion est présent
        $this->assertSelectorExists('form.login-form');
        // On verifie que les champs email et password sont présents
        $this->assertSelectorExists('input[name="_username"]');
        // On verifie que les champs email et password sont présents
        $this->assertSelectorExists('input[name="_password"]');
        // On verifie que le h1 de connexion est présent
        $this->assertSelectorTextContains('h1', 'connexion');    
    }

    public function testLoginRedirectsIfAlreadyAuthenticated(): void
    { // 1; créé un client HTTP simulé
        $client = static::createClient();
        //simule  un user connecté
        $container = static::getContainer();
    //    $client->loginUser('test@test.com', 'password');
       // on effectue une requete GET vers l url '/login'
        // $client->request('GET', '/login');
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        // 3 asertion: //$this->assertResponseRedirects('/home",302);
        // On verifie le code 302 de la redicrection reussie et la destination
        //$this->assertResponseIsSuccessful();
            $user = $userProvider->loadUserByIdentifier('test@test.com');
            $client->loginUser($user);
            $client->request('GET', '/login');
            $this->assertResponseRedirects();
    }

    public function testLogoutWorks(): void
    { // 1; créé un client HTTP simulé
        $client = static::createClient();
        //simule  un user connecté
        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);
        $client->request('GET', '/logout');
        $this->assertResponseRedirects();
    }
}
