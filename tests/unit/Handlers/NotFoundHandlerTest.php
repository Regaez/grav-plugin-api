<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Handlers\NotFoundHandler;

final class NotFoundHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var NotFoundHandler $handler */
    protected $handler;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->handler = new NotFoundHandler();
        $this->response = new Response();
    }

    public function testNotFoundHandlerShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/blah'
            ])
        );

        $response = $this->handler->__invoke($request, $this->response, []);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
