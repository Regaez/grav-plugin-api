<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GuzzleHttp\Client;

final class PagesHandlerTest extends Test
{
    /** @var Client $client */
    protected $client;

    protected function _before()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost/api/',
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false
        ]);
    }

    public function testGetPagesShouldReturnStatus200(): void
    {
        $response = $this->client->get('pages');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageShouldReturnStatus200(): void
    {
        $response = $this->client->get('pages/test');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageShouldReturnStatus404(): void
    {
        $response = $this->client->get('pages/blarg');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus401IfNoAuth(): void
    {
        $response = $this->client->post('pages', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => [
                    'title' => 'Test page'
                ]
            ])
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus400IfNoRouteGiven(): void
    {
        $response = $this->client->post('pages', [
            'body' => json_encode([
                'header' => [
                    'title' => 'Test page'
                ]
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus400IfHeaderNotJson(): void
    {
        $response = $this->client->post('pages', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => 'invalid!'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus403IfPageExists(): void
    {
        $response = $this->client->post('pages', [
            'body' => json_encode([
                'route' => '/test',
                'header' => [
                    'title' => 'Test page'
                ]
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus200(): void
    {
        $response = $this->client->post('pages', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => [
                    'title' => 'Test page'
                ]
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus401IfNoAuth(): void
    {
        $response = $this->client->patch('pages/test-page', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => [
                    'title' => 'Test page'
                ]
            ])
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus400IfNoRouteGiven(): void
    {
        $response = $this->client->patch('pages/test-page', [
            'body' => json_encode([
                'header' => [
                    'title' => 'Test page'
                ]
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus400IfHeaderNotJson(): void
    {
        $response = $this->client->patch('pages/test-page', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => 'invalid!'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus404IfPageIsNotFound(): void
    {
        $response = $this->client->patch('pages/non-existent-page', [
            'body' => json_encode([
                'route' => '/non-existent-page',
                'header' => [
                    'title' => 'Test page'
                ]
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus200(): void
    {
        $response = $this->client->patch('pages/test-page', [
            'body' => json_encode([
                'route' => '/test-page',
                'header' => [
                    'title' => 'Test page'
                ],
                'content' => 'This is some new content'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeletePageShouldReturnStatus401(): void
    {
        $response = $this->client->delete('pages/test-page');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testDeletePageShouldReturnStatus404(): void
    {
        $response = $this->client->delete('pages/blarg', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeletePageShouldReturnStatus204(): void
    {
        $response = $this->client->delete('pages/test-page', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
