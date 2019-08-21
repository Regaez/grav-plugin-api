<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GuzzleHttp\Client;

final class UsersHandlerTest extends Test
{
    /** @var Client $client */
    protected $client;

    protected function _before()
    {
        $this->client = new Client([
            'base_uri' => 'localhost/api/',
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false
        ]);
    }

    public function testGetUsersShouldReturnStatus200(): void
    {
        $response = $this->client->get('users', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldReturnStatus200(): void
    {
        $response = $this->client->get('users/development', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldReturnStatus404(): void
    {
        $response = $this->client->get('users/blarg', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus401IfNoAuth(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'username' => 'testuser',
                'password' => 'Passw0rd!',
                'email' => 'testuser@test.com'
            ])
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoUsernameGiven(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'password' => 'Passw0rd!',
                'email' => 'testuser@test.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoPasswordGiven(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'username' => 'testuser',
                'email' => 'testuser@test.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus400IfNoEmailGiven(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'username' => 'testuser',
                'password' => 'Passw0rd!',
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus403IfUserExists(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'username' => 'development',
                'password' => 'Passw0rd!',
                'email' => 'testuser@test.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testNewUserShouldReturnStatus200(): void
    {
        $response = $this->client->post('users', [
            'body' => json_encode([
                'username' => 'testuser',
                'password' => 'Passw0rd!',
                'email' => 'testuser@test.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus401IfNoAuth(): void
    {
        $response = $this->client->patch('users/testuser', [
            'body' => json_encode([
                'email' => 'testuser2@test.com'
            ])
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus400IfNoPasswordGiven(): void
    {
        $response = $this->client->patch('users/testuser', [
            'body' => json_encode([
                'new_password' => 'Blarg123!'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus400IfBadPasswordGiven(): void
    {
        $response = $this->client->patch('users/testuser', [
            'body' => json_encode([
                'new_password' => 'Blarg123!',
                'password' => 'thisiswrong'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus404IfNoUserFound(): void
    {
        $response = $this->client->patch('users/blarg', [
            'body' => json_encode([
                'username' => 'blarg',
                'password' => 'Passw0rd!',
                'email' => 'blarg@email.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdateUserShouldReturnStatus200(): void
    {
        $response = $this->client->patch('users/testuser', [
            'body' => json_encode([
                'new_password' => 'This1sANewPassword!',
                'password' => 'Passw0rd!',
                'email' => 'testuser2@test.com'
            ]),
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteUserShouldReturnStatus401(): void
    {
        $response = $this->client->delete('users/testuser');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testDeleteUserShouldReturnStatus404(): void
    {
        $response = $this->client->delete('users/blarg', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteUserShouldReturnStatus204(): void
    {
        $response = $this->client->delete('users/testuser', [
            'auth' => ['development', 'D3velopment']
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
