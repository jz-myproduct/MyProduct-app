<?php

namespace App\Tests\FrontOffice;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Welcome on MyProduct!');
    }
}