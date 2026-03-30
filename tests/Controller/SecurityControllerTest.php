<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoadsForAnonymousUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testLoginRedirectsIfAlreadyAuthenticated(): void
    {
        $client = static::createClient();

        $client->loginUser('test@test.com', 'password');
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }
}
