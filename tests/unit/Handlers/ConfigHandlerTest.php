<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Handlers\ConfigHandler;

final class ConfigHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var ConfigHandler $handler */
    protected $handler;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->handler = new ConfigHandler();
        $this->response = new Response();
    }

    protected function _after()
    {
        Config::resetInstance();
    }

    public function testGetConfigsShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/configs'
            ])
        );

        $response = $this->handler->getConfigs(
            $request,
            $this->response,
            []
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetConfigShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/configs/site'
            ])
        );

        $response = $this->handler->getConfigs(
            $request,
            $this->response,
            []
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetConfigShouldReturnStatus400IfNoIdProvided(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/configs'
            ])
        );

        $response = $this->handler->getConfig(
            $request,
            $this->response,
            []
        );

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetConfigShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/configs/blarg'
            ])
        );

        $response = $this->handler->getConfig(
            $request,
            $this->response,
            [
                'config' => 'blarg'
            ]
        );

        $this->assertEquals(404, $response->getStatusCode());
    }
}
