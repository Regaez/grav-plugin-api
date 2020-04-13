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
use GravApi\Config\Constants;
use GravApi\Handlers\PagesHandler;

final class PagesHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var PagesHandler $handler */
    protected $handler;

    protected function _before($attributes = array(), $endpoints = array())
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        // initialise our default test config
        $config = [
            'endpoints' => [
                Constants::ENDPOINT_PAGE => [
                    Constants::METHOD_GET => [
                        'enabled' => true,
                        'auth' => false,
                        'fields' => $attributes
                    ],
                    Constants::METHOD_PATCH => [
                        'enabled' => true,
                        'auth' => false
                    ],
                    Constants::METHOD_POST => [
                        'enabled' => true,
                        'auth' => false
                    ],
                    Constants::METHOD_DELETE => [
                        'enabled' => true,
                        'auth' => false
                    ]
                ]
            ]
        ];

        // override our default config if custom endpoints are given
        if (count($endpoints) > 0) {
            $config['endpoints'] = $endpoints;
        }

        Config::instance($config);

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

    public function testGetPagesAuthShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages'
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPages($request, $this->response, [
            'page' => 'test'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPagesAdvancedAccessShouldReturnLimitedPages(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages',
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('greg');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPages($request, $this->response, [
            'page' => 'test'
        ]);

        $data = json_decode($response->getBody()->__toString());
        $this->assertEquals(2, $data->meta->count);
    }

    public function testGetPagesAdvancedAccessShouldReturnEmptyArray(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages',
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $user->set('api.advanced_access.pages.get', []);
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPages($request, $this->response, [
            'page' => 'home'
        ]);

        $data = json_decode($response->getBody()->__toString());
        $this->assertEquals(0, $data->meta->count);
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

    public function testGetPageAuthShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages/test'
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'test'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageAdvancedAccessShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages/test',
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'test'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetPageAdvancedAccessShouldReturnStatus401(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_GET => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/pages/home',
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'home'
        ]);

        $this->assertEquals(401, $response->getStatusCode());
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
                'REQUEST_URI' => '/api/pages/searches'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'category' => ['blog'],
                'tag' => ['news', 'grav']
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
                'REQUEST_URI' => '/api/pages/searches'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'category' => ['blog']
            ],
            'operation' => 'and'
        ]);

        $response = $this->handler->findPages($request, $this->response, []);

        $this->assertEquals(2, count(json_decode($response->getBody()->__toString())->items));
    }

    public function testFindPagesShouldReturnOnePage(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages/searches'
            ])
        )->withParsedBody([
            'taxonomyFilter' => [
                'tag' => ['news']
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

    public function testNewPageShouldSetPageOrder(): void
    {
        $this->_before(['order', 'folder', 'route', 'slug']);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test-order',
            'header' => [
                'title' => 'Test page'
            ],
            'order' => 1
        ]);

        $response = $this->handler->newPage($request, $this->response, []);

        $data = json_decode($response->getBody()->__toString());

        $this->assertEquals('01.', $data->attributes->order);
        $this->assertEquals('01.test-order', $data->attributes->folder);
        $this->assertEquals('test-order', $data->attributes->slug);
        $this->assertEquals('/test-order', $data->attributes->route);
    }

    public function testNewPageAuthShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_POST => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test-auth',
            'header' => [
                'title' => 'Test page'
            ],
            'order' => 1
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewPageAdvancedAccessShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_POST => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages',
            ])
        )->withParsedBody([
            'route' => '/test/advanced-auth',
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewPageAdvancedAccessShouldReturnStatus401(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_POST => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/pages',
            ])
        )->withParsedBody([
            'route' => '/unauthorised-route',
            'header' => [
                'title' => 'Test page'
            ],
            'order' => 1
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->newPage($request, $this->response, []);

        $this->assertEquals(401, $response->getStatusCode());
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

    public function testUpdatePageShouldChangePageOrder(): void
    {
        $this->_before(['order', 'folder', 'route', 'slug']);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/test-order'
            ])
        )->withParsedBody([
            'route' => '/test-order',
            'header' => [
                'title' => 'Test page'
            ],
            'order' => 2
        ]);

        $response = $this->handler->updatePage(
            $request,
            $this->response,
            [ 'page' => 'test-order' ]
        );

        $data = json_decode($response->getBody()->__toString());
        $this->assertEquals('02.', $data->attributes->order);
        $this->assertEquals('02.test-order', $data->attributes->folder);
        $this->assertEquals('test-order', $data->attributes->slug);
        $this->assertEquals('/test-order', $data->attributes->route);
    }

    public function testUpdatePageShouldRemovePageOrder(): void
    {
        $this->_before(['order', 'folder', 'route', 'slug']);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages/test-order'
            ])
        )->withParsedBody([
            'route' => '/test-order',
            'header' => [
                'title' => 'Test page'
            ],
            'order' => false
        ]);

        $response = $this->handler->updatePage(
            $request,
            $this->response,
            [ 'page' => 'test-order' ]
        );

        $data = json_decode($response->getBody()->__toString());
        $this->assertEquals(false, $data->attributes->order);
        $this->assertEquals('test-order', $data->attributes->folder);
        $this->assertEquals('test-order', $data->attributes->slug);
        $this->assertEquals('/test-order', $data->attributes->route);
    }

    public function testUpdatePageAuthShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_PATCH => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages'
            ])
        )->withParsedBody([
            'route' => '/test-page',
            'header' => [
                'title' => 'Test page 2',
                'taxonomy' => [
                    'category' => [
                        'blog'
                    ]
                ]
            ],
            'content' => 'This is some new authored content'
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'test-page'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdatePageAdvancedAccessShouldReturnStatus200(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_PATCH => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages',
            ])
        )->withParsedBody([
            'route' => '/test',
            'header' => [
                'title' => 'Test page'
            ]
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'test'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdatePageAdvancedAccessShouldReturnStatus401(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_PATCH => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/pages',
            ])
        )->withParsedBody([
            'route' => '/home',
            'header' => [
                'title' => 'Change title',
            ],
            'content' => 'This is some failed content update'
        ]);

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->getPage($request, $this->response, [
            'page' => 'home'
        ]);

        $this->assertEquals(401, $response->getStatusCode());
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
        $requestTest = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/test-page'
            ])
        );
        $requestOrder = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/test-order'
            ])
        );

        $responseTest = $this->handler->deletePage(
            $requestTest,
            $this->response,
            [ 'page' => 'test-page' ]
        );
        $responseOrder = $this->handler->deletePage(
            $requestOrder,
            $this->response,
            [ 'page' => 'test-order' ]
        );

        $this->assertEquals(204, $responseTest->getStatusCode());
        $this->assertEquals(204, $responseOrder->getStatusCode());
    }

    public function testDeletePageAuthShouldReturnStatus204(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_DELETE => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/test-auth'
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->deletePage(
            $request,
            $this->response,
            [ 'page' => 'test-auth' ]
        );

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeletePageAdvancedAuthShouldReturnStatus204(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_DELETE => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/test/advanced-auth'
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->deletePage(
            $request,
            $this->response,
            [ 'page' => 'test/advanced-auth' ]
        );

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeletePageAdvancedAuthShouldReturnStatus401(): void
    {
        $this->_before([], [
            Constants::ENDPOINT_PAGE => [
                Constants::METHOD_DELETE => [
                    'enabled' => true,
                    'auth' => true
                ]
            ]
        ]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/pages/home'
            ])
        );

        // Decorate the request with the user (which AuthMiddleware would usually do)
        $user = $this->grav['accounts']->load('tom');
        $user->authenticated = true;
        $request = $request->withAttribute('user', $user);

        $response = $this->handler->deletePage(
            $request,
            $this->response,
            [ 'page' => 'home' ]
        );

        $this->assertEquals(401, $response->getStatusCode());
    }
}
