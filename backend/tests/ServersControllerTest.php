<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServersControllerTest extends WebTestCase
{
    public function testRequestWithoutParameters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/servers');

        $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

    }

    public function testRequestWithInvalidParameterFails(){

        $client = static::createClient();
        $client->request('GET', '/servers?page=invalid_page_value');

        $this->assertResponseIsUnprocessable();
    }

}
