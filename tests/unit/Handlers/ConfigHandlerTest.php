<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GuzzleHttp\Client;

final class ConfigHandlerTest extends Test
{
    /** @var Client $client */
    protected $client;

    protected function _before()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost/api/',
            'http_errors' => false
        ]);
    }

    public function testGetConfigsShouldReturnStatus200(): void
    {
        $response = $this->client->get('configs', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetConfigShouldReturnStatus200(): void
    {
        $response = $this->client->get('configs/site', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetConfigShouldReturnStatus404(): void
    {
        $response = $this->client->get('configs/blarg', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
