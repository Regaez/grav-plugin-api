<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GuzzleHttp\Client;

final class PluginsHandlerTest extends Test
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

    public function testGetPluginsShouldReturnStatus200(): void
    {
        $response = $this->client->get('plugins', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPluginsShouldReturnStatus401(): void
    {
        $response = $this->client->get('plugins');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testGetPluginShouldReturnStatus200(): void
    {
        $response = $this->client->get('plugins/api', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPluginShouldReturnStatus401(): void
    {
        $response = $this->client->get('plugins/api');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testGetPluginShouldReturnStatus404(): void
    {
        $response = $this->client->get('plugins/blarg', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
