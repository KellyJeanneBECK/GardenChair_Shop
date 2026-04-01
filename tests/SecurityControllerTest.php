<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    // public function testSomething(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/');

    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h1', 'Hello World');
    // }

    public function testLoginPageLoadsForAnonymousUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form.login-form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLoginRedirectsIfAlreadyAUthenticated(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);
        $client->request('GET', '/login');
        $this->assertResponseRedirects();
    }

    public function testLogoutWorks(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider_test');
        $user = $userProvider->loadUserByIdentifier('test@test.com');
        $client->loginUser($user);
        $client->request('GET', '/logout');
        $this->assertResponseRedirects();
    }
}