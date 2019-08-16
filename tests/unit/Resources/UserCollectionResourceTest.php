<?php

declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Resources\UserResource;
use GravApi\Resources\UserCollectionResource;
use GravApi\Config\Config;

final class UserCollectionResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var UserCollectionResource $resource */
    protected $resource;

    protected $username = 'development';

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance([
            'api' => [
                'route' => 'api',
                'permalink' => 'http://localhost/api',
            ],
            'users' => [
                'get' => [
                    'enabled' => true,
                    'fields' => []
                ]
            ]
        ]);

        $this->user = $this->grav['accounts']->load($this->username);
        $this->resource = new UserCollectionResource([ $this->user ]);
    }

    public function testGetResourceReturnsUserResource(): void
    {
        $this->assertInstanceOf(
            UserResource::class,
            $this->resource->getResource($this->user)
        );
    }

    public function testToJsonReturnsItemsArray(): void
    {
        $this->assertIsArray(
            $this->resource->toJson()['items']
        );
    }

    public function testToJsonReturnsMeta(): void
    {
        $this->assertIsArray(
            $this->resource->toJson()['meta']
        );
    }

    public function testToJsonReturnsMetaCount(): void
    {
        $result = $this->resource->toJson()['meta']['count'];
        $this->assertEquals(1, $result);
    }
}
