<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GravApi\Config\Constants;
use GravApi\Config\Method;
use GravApi\Config\Endpoint;

final class EndpointTest extends Test
{
    public function testCreatesDefaultMethods(): void
    {
        $endpoint = new Endpoint();
        $this->assertInstanceOf(Method::class, $endpoint->get);
        $this->assertInstanceOf(Method::class, $endpoint->post);
        $this->assertInstanceOf(Method::class, $endpoint->patch);
        $this->assertInstanceOf(Method::class, $endpoint->delete);
    }

    public function testUnknownPropertiesReturnNull(): void
    {
        $endpoint = new Endpoint();
        $this->assertNull($endpoint->randomProperty);
    }

    public function testCanConfigureGetMethod(): void
    {
        $endpoint = new Endpoint([
            Constants::METHOD_GET => [
                'enabled' => true,
                'auth' => false,
                'fields' => [
                    'one',
                    'two'
                ],
                'ignore_files' => [
                    'three',
                    'four'
                ]
            ]
        ]);

        $this->assertInstanceOf(Method::class, $endpoint->get);
        $this->assertTrue($endpoint->get->enabled);
        $this->assertFalse($endpoint->get->useAuth);
        $this->assertEquals(['one', 'two'], $endpoint->get->fields);
        $this->assertEquals(['three', 'four'], $endpoint->get->ignore_files);
    }

    public function testCanConfigurePostMethod(): void
    {
        $endpoint = new Endpoint([
            Constants::METHOD_POST => [
                'enabled' => true,
                'auth' => false,
                'fields' => [
                    'one',
                    'two'
                ],
                'ignore_files' => [
                    'three',
                    'four'
                ]
            ]
        ]);

        $this->assertInstanceOf(Method::class, $endpoint->post);
        $this->assertTrue($endpoint->post->enabled);
        $this->assertFalse($endpoint->post->useAuth);
        $this->assertEquals(['one', 'two'], $endpoint->post->fields);
        $this->assertEquals(['three', 'four'], $endpoint->post->ignore_files);
    }

    public function testCanConfigurePatchMethod(): void
    {
        $endpoint = new Endpoint([
            Constants::METHOD_PATCH => [
                'enabled' => true,
                'auth' => false,
                'fields' => [
                    'one',
                    'two'
                ],
                'ignore_files' => [
                    'three',
                    'four'
                ]
            ]
        ]);

        $this->assertInstanceOf(Method::class, $endpoint->patch);
        $this->assertTrue($endpoint->patch->enabled);
        $this->assertFalse($endpoint->patch->useAuth);
        $this->assertEquals(['one', 'two'], $endpoint->patch->fields);
        $this->assertEquals(['three', 'four'], $endpoint->patch->ignore_files);
    }

    public function testCanConfigureDeleteMethod(): void
    {
        $endpoint = new Endpoint([
            Constants::METHOD_DELETE => [
                'enabled' => true,
                'auth' => false,
                'fields' => [
                    'one',
                    'two'
                ],
                'ignore_files' => [
                    'three',
                    'four'
                ]
            ]
        ]);

        $this->assertInstanceOf(Method::class, $endpoint->delete);
        $this->assertTrue($endpoint->delete->enabled);
        $this->assertFalse($endpoint->delete->useAuth);
        $this->assertEquals(['one', 'two'], $endpoint->delete->fields);
        $this->assertEquals(['three', 'four'], $endpoint->delete->ignore_files);
    }
}
