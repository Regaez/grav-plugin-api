<?php

declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use Grav\Common\User\User;
use GravApi\Api;
use GravApi\Resources\UserResource;
use GravApi\Config\Constants;
use GravApi\Config\Config;

final class UserResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var User $user */
    protected $user;

    /** @var UserResource $resource */
    protected $resource;

    /** @var Api $api */
    protected $api;

    protected $id = 'development';

    protected function _before($attributeFields = array())
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance([
            'endpoints' => [
                Constants::ENDPOINT_USER => [
                    Constants::METHOD_GET => [
                        'enabled' => true,
                        'fields' => $attributeFields
                    ]
                ]
            ]
        ]);

        $this->user = $this->grav['accounts']->load($this->id);
        $this->resource = new UserResource($this->user);
    }

    public function testGetIdReturnsPluginName(): void
    {
        $this->assertEquals(
            $this->id,
            $this->resource->getId()
        );
    }

    public function testGetResourceTypeReturnsUser(): void
    {
        $this->assertEquals(
            Constants::TYPE_USER,
            $this->resource->getResourceType()
        );
    }

    public function testGetResourceAttributesReturnsUserData(): void
    {
        $attributes = [
            'username' => 'development',
            'email' => 'dummy@email.com',
            'fullname' => 'Development',
            'title' => 'Administrator',
            'state' => 'enabled',
            'access' => [
                'admin' => [
                    'login' => true,
                    'super' => true
                ],
                'site' => [
                    'login' => true
                ]
            ],
            'groups' => null
        ];

        $this->assertEquals(
            $attributes,
            $this->resource->getResourceAttributes()
        );
    }


    public function testGetResourceEndpointReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/users/',
            $this->resource->getResourceEndpoint()
        );
    }

    public function testGetRelatedSelfReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/users/development',
            $this->resource->getRelatedSelf()
        );
    }

    public function testGetRelatedHypermediaReturnsSelfAndResourceUrls(): void
    {
        $expected = [
            'self' => 'http://localhost/api/users/development',
            'resource' => 'http://localhost/api/users/'
        ];

        $this->assertEquals(
            $expected,
            $this->resource->getRelatedHypermedia()
        );
    }

    public function testGetHypermediaReturnsSelfAndRelatedHypermedia(): void
    {
        $expected = [
            'related' => [
                'self' => 'http://localhost/api/users/development',
                'resource' => 'http://localhost/api/users/'
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->getHypermedia()
        );
    }

    public function testToJsonReturnsResourceObject(): void
    {
        $attributes = [
            'username' => 'development',
            'email' => 'dummy@email.com',
            'fullname' => 'Development',
            'title' => 'Administrator',
            'state' => 'enabled',
            'access' => [
                'admin' => [
                    'login' => true,
                    'super' => true
                ],
                'site' => [
                    'login' => true
                ]
            ],
            'groups' => null
        ];

        $expected = [
            'type' => Constants::TYPE_USER,
            'id' => 'development',
            'attributes' => $attributes,
            'links' => [
                'related' => [
                    'self' => 'http://localhost/api/users/development',
                    'resource' => 'http://localhost/api/users/'
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->toJson()
        );
    }

    public function testToJsonReturnsAttributesOnly(): void
    {
        $expected = [
            'username' => 'development',
            'email' => 'dummy@email.com',
            'fullname' => 'Development',
            'title' => 'Administrator',
            'state' => 'enabled',
            'access' => [
                'admin' => [
                    'login' => true,
                    'super' => true
                ],
                'site' => [
                    'login' => true
                ]
            ],
            'groups' => null
        ];;

        $this->assertEquals(
            $expected,
            $this->resource->toJson(true)
        );
    }

    public function testSetFilterReturnsSpecificResourceFields(): void
    {
        $this->_before(['email', 'title', 'state']);

        $attributes = [
            'email' => 'dummy@email.com',
            'title' => 'Administrator',
            'state' => 'enabled'
        ];

        $expected = [
            'type' => Constants::TYPE_USER,
            'id' => 'development',
            'attributes' => $attributes,
            'links' => [
                'related' => [
                    'self' => 'http://localhost/api/users/development',
                    'resource' => 'http://localhost/api/users/'
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->toJson()
        );
    }

    public function testCanReturnCustomUserFields(): void
    {
        $this->_before(['custom']);

        $attributes = [
            'custom' => 'this is a custom field'
        ];

        $expected = [
            'type' => Constants::TYPE_USER,
            'id' => 'development',
            'attributes' => $attributes,
            'links' => [
                'related' => [
                    'self' => 'http://localhost/api/users/development',
                    'resource' => 'http://localhost/api/users/'
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->toJson()
        );
    }
}
