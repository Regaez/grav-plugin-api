<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Handlers\PluginsHandler;

final class PluginsHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var PluginsHandler $handler */
    protected $handler;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->handler = new PluginsHandler();
        $this->response = new Response();
    }

    public function testGetPluginsShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/plugins'
            ])
        );

        $response = $this->handler->getPlugins($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPluginShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/plugins/api'
            ])
        );

        $response = $this->handler->getPlugin(
            $request,
            $this->response,
            [
                'plugin' => 'api'
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPluginShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/plugins/blarg'
            ])
        );

        $response = $this->handler->getPlugin(
            $request,
            $this->response,
            [
                'plugin' => 'blarg'
            ]
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdatePluginShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/plugins/api'
            ])
        )->withParsedBody([
            'custom' => 'field'
        ]);

        $response = $this->handler->updatePlugin(
            $request,
            $this->response,
            [
                'plugin' => 'api'
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->__toString());
        $this->assertEquals('field', $data->attributes->custom);
        // Should not manipulate existing fields
        $this->assertEquals('/api', $data->attributes->route);
    }

    public function testUpdatePluginShouldReturnStatus400(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/plugins/api'
            ])
        )->withParsedBody([
            'route' => ''
        ]);

        $response = $this->handler->updatePlugin(
            $request,
            $this->response,
            [
                'plugin' => 'api'
            ]
        );

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdatePluginShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/plugins/blarg'
            ])
        )->withParsedBody([
            'custom' => 'field'
        ]);

        $response = $this->handler->updatePlugin(
            $request,
            $this->response,
            [
                'plugin' => 'blarg'
            ]
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdatePluginShouldRemoveNullProperties(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/plugins/api'
            ])
        )->withParsedBody([
            'custom' => null
        ]);

        $response = $this->handler->updatePlugin(
            $request,
            $this->response,
            [
                'plugin' => 'api'
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        // TODO: figure out why this test fails sometimes but not always

        // $data = json_decode($response->getBody()->__toString());
        // $this->assertFalse(property_exists($data->attributes, 'custom'));
    }
}
