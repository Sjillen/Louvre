<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerTest extends WebTestCase
{
   


    public function testHome()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/home');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }

    public function testBooking()
    {
    	$client = static::createClient();

        $crawler = $client->request('GET', '/booking');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test404()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
    }
   

}
