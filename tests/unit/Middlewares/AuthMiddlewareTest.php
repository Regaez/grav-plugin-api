<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Middlewares\AuthMiddleware;

final class AuthMiddlewareTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var AuthMiddleware $middleware */
    protected $middleware;

    /** @var callable $next Next middleware */
    protected $next;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        $this->middleware = new AuthMiddleware(
            Config::instance()->users->get,
            [Constants::ROLE_USERS_READ]
        );

        $this->next = function ($request, $response) {
            return $response->withJson('', 200);
        };
    }

    public function testAdminSuperRoleShouldReturnStatus200(): void
    {
        // User 'development' has admin super permissions
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'development',
                'PHP_AUTH_PW' => 'D3velopment'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSpecificRoleShouldReturnStatus200(): void
    {
        // User 'percy' has specific role permission
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'percy',
                'PHP_AUTH_PW' => 'D3velopment'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGroupInheritedRoleShouldReturnStatus200(): void
    {
        // User 'andy' should inherit permission from his group
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'andy',
                'PHP_AUTH_PW' => 'D3velopment'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNoRoleShouldReturnStatus401(): void
    {
        // User 'joe' does not have any API permissions
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'joe',
                'PHP_AUTH_PW' => 'D3velopment'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNoCredentialsShouldReturnStatus401(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testIncorrectPasswordShouldReturnStatus401(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'development',
                'PHP_AUTH_PW' => 'incorrect'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNonExistentUserShouldReturnStatus401(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users',
                'PHP_AUTH_USER' => 'anonymous',
                'PHP_AUTH_PW' => 'D3velopment'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSessionUserWithAdminRoleShouldReturnStatus200(): void
    {
        // Sets a user to be "logged in" and authenticated
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;
        $this->grav['session']->user = $user;

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSessionUserWithoutRolesShouldReturnStatus401(): void
    {
        // Sets a user to be "logged in" and authenticated
        $user = $this->grav['accounts']->load('joe');
        $user->authenticated = true;
        $this->grav['session']->user = $user;

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSessionUserInGroupShouldReturnStatus200(): void
    {
        // Sets a user to be "logged in" and authenticated
        $user = $this->grav['accounts']->load('andy');
        $user->authenticated = true;
        $this->grav['session']->user = $user;

        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users'
            ])
        );

        $response = $this->middleware->__invoke(
            $request,
            new Response(),
            $this->next
        );

        $this->assertEquals(200, $response->getStatusCode());
    }
}
