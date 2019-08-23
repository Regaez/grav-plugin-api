<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Handlers\UsersHandler;

final class UsersHandlerTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    /** @var Response $response */
    protected $response;

    /** @var UsersHandler $handler */
    protected $handler;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->handler = new UsersHandler();
        $this->response = new Response();
    }

    public function testGetUsersShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users'
            ])
        );

        $response = $this->handler->getUsers($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users/development'
            ])
        );

        $response = $this->handler->getUser($request, $this->response, [
            'user' => 'development'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/api/users/blarg'
            ])
        );

        $response = $this->handler->getUser($request, $this->response, [
            'user' => 'blarg'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoUsernameGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/users'
            ])
        )->withParsedBody([
            'password' => 'Passw0rd!',
            'email' => 'testuser@test.com'
        ]);

        $response = $this->handler->newUser($request, $this->response, []);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoPasswordGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/users'
            ])
        )->withParsedBody([
            'username' => 'testuser',
            'email' => 'testuser@test.com'
        ]);

        $response = $this->handler->newUser($request, $this->response, []);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoEmailGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/users'
            ])
        )->withParsedBody([
            'username' => 'testuser',
            'password' => 'Passw0rd!'
        ]);

        $response = $this->handler->newUser($request, $this->response, []);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus403IfUserExists(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/users'
            ])
        )->withParsedBody([
            'username' => 'development',
            'password' => 'Passw0rd!',
            'email' => 'testuser@test.com'
        ]);

        $response = $this->handler->newUser($request, $this->response, []);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/users'
            ])
        )->withParsedBody([
            'username' => 'testuser',
            'password' => 'Passw0rd!',
            'email' => 'testuser@test.com'
        ]);

        $response = $this->handler->newUser($request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus400IfNoPasswordGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/users/testuser'
            ])
        )->withParsedBody([
            'new_password' => 'Blarg123!'
        ]);

        $response = $this->handler->updateUser($request, $this->response, [
            'user' => 'testuser'
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus400IfBadPasswordGiven(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/users/testuser'
            ])
        )->withParsedBody([
            'new_password' => 'Blarg123!',
            'password' => 'thisiswrong'
        ]);

        $response = $this->handler->updateUser($request, $this->response, [
            'user' => 'testuser'
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus404IfNoUserFound(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/users/blarg'
            ])
        )->withParsedBody([
            'password' => 'Passw0rd!',
            'email' => 'blarg@email.com'
        ]);

        $response = $this->handler->updateUser($request, $this->response, [
            'user' => 'blarg'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus200(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/api/users/testuser'
            ])
        )->withParsedBody([
            'new_password' => 'This1sANewPassword!',
            'password' => 'Passw0rd!',
            'email' => 'testuser2@test.com'
        ]);

        $response = $this->handler->updateUser($request, $this->response, [
            'user' => 'testuser'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteUserShouldReturnStatus404(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/users/blarg'
            ])
        );

        $response = $this->handler->deleteUser($request, $this->response, [
            'user' => 'blarg'
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteUserShouldReturnStatus204(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/api/users/testuser'
            ])
        );

        $response = $this->handler->deleteUser($request, $this->response, [
            'user' => 'testuser'
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
