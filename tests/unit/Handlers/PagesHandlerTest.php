<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Handlers\PagesHandler;

final class PagesHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var PagesHandler $handler */
    protected $handler;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->handler = new PagesHandler();
        $this->response = new Response();
    }

    public function testGetPagesShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages'
            ])
        );

        $response = $this->handler->getPages($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages/test'
            ])
        );

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'test'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages/blarg'
            ])
        );

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'blarg'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testFindPagesShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'taxonomyKey1' => ['taxonomyValue1'],
                'taxonomyKey2' => ['taxonomyValue2', 'taxonomyValue3']
            ],
            'operation' => 'and'
        ]);

        $response = $this->handler->findPages($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFindPagesShouldReturnTwoPages(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'taxonomyKey1' => ['taxonomyValue1']
            ],
            'operation' => 'and'
        ]);

        $response = $this->handler->findPages($request, $this->response, []);

        var_dump(json_decode($response->getBody()->__toString())->items);
        $this->assertEquals(2, count(json_decode($response->getBody()->__toString())->items));
    }

    public function testFindPagesShouldReturnOnePage(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'taxonomyKey2' => ['taxonomyValue2']
            ],
            'operation' => 'or'
        ]);

        $response = $this->handler->findPages($request, $this->response, []);

        $this->assertEquals(1, count(json_decode($response->getBody()->__toString())->items));
    }

    public function testNewPageShouldReturnStatus400IfNoRouteGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus400IfHeaderNotJson(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test-page',
            'header' => 'invalid!'
        ]);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus403IfPageExists(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test',
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testNewPageShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test-page',
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus400IfNoRouteGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/test-page'
            ])
        )->withParsedBody([
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        $response = $this->handler->updatePage($request, $this->response, [
            'page' => 'test-page'
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus400IfHeaderNotJson(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/test-page'
            ])
        )->withParsedBody([
            'route' => '/test-page',
            'header' => 'invalid!'
        ]);

        $response = $this->handler->updatePage($request, $this->response, [
            'page' => 'test-page'
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus404IfPageIsNotFound(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/non-existent-page'
            ])
        )->withParsedBody([
            'route' => '/non-existent-page',
            'header' => 'invalid!'
        ]);

        $response = $this->handler->updatePage($request, $this->response, [
            'page' => 'non-existent-page'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdatePageShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/test-page'
            ])
        )->withParsedBody([
            'route' => '/test-page',
            'header' => [
                'title' => 'Test page'
            ],
            'content' => 'This is some new content'
        ]);

        $response = $this->handler->updatePage($request, $this->response, [
            'page' => 'test-page'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeletePageShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/blarg'
            ])
        );

        $response = $this->handler->deletePage($request, $this->response, [
            'page' => 'blarg'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeletePageShouldReturnStatus204(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/test-page'
            ])
        );

        $response = $this->handler->deletePage($request, $this->response, [
            'page' => 'test-page'
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
