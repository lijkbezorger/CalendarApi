<?php

namespace Tests\EventController\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tests\ApiTestCaseController;

class ReminderControllerTest extends ApiTestCaseController
{
    public function testIndex()
    {
        $token = $this->authorize('lijkbezorger', 'r700i@i.ua');

        $crawler = $this->client->request(
            'GET',
            '/api/events/reminders',
            [],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('title', $response->getContent());
    }

    public function testCreate()
    {
        $token = $this->authorize('lijkbezorger', 'r700i@i.ua');

        $content = json_encode([
            'title' => 'title1',
            'description' => 'description1',
            'startDateTime' => '10.07.2018 18:00:00',
            'endDateTime' => '10.07.2018 19:00:00',
            'user' => [
                'id' => 13,
            ]
        ]);

        $crawler = $this->client->request(
            'POST',
            '/api/events/reminders',
            [],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token,
            ],
            $content
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testView()
    {
        $token = $this->authorize('lijkbezorger', 'r700i@i.ua');

        $crawler = $this->client->request(
            'GET',
            '/api/events/reminders',
            [
                'id' => 2,
            ],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('title1', $response->getContent());
    }

    //405
    public function testUpdate()
    {
        $token = $this->authorize('lijkbezorger', 'r700i@i.ua');

        $content = json_encode([
            'title' => 'title1',
            'description' => 'description2',
        ]);

        $crawler = $this->client->request(
            'PUT',
            '/api/events/reminders',
            [
                'id' => 2
            ],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token,
            ],
            $content
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    //405
    public function testDelete()
    {
        $token = $this->authorize('lijkbezorger', 'r700i@i.ua');

        $crawler = $this->client->request(
            'DELETE',
            '/api/events/reminders',
            [
                'id' => 2
            ],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
