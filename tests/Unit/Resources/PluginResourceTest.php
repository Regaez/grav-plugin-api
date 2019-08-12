<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use Grav\Common\Plugin;
use GravApi\Api;
use GravApi\Resources\PluginResource;
use GravApi\Config\Constants;
use GravApi\Config\Config;

final class PluginResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var Plugin $plugin */
    protected $plugin;

    /** @var PluginResource $resource */
    protected $resource;

    /** @var Api $api */
    protected $api;

    protected $id = 'api';

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance([
            'api' => [
                'route' => 'api',
                'permalink' => 'http://localhost/api'
            ]
        ]);

        foreach ($this->grav['plugins'] as $plugin) {
            if ($plugin->name === $this->id) {
                $this->plugin = $plugin;
                $this->resource = new PluginResource($plugin);
            }
        }
    }

    public function testGetIdReturnsPluginName(): void
    {
        $this->assertEquals(
            $this->id,
            $this->resource->getId()
        );
    }

    public function testGetResourceTypeReturnsPlugin(): void
    {
        $this->assertEquals(
            Constants::TYPE_PLUGIN,
            $this->resource->getResourceType()
        );
    }

    public function testGetResourceAttributesReturnsPluginConfig(): void
    {
        $this->assertEquals(
            $this->plugin->config(),
            $this->resource->getResourceAttributes()
        );
    }

    public function testGetResourceEndpointReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/plugins/',
            $this->resource->getResourceEndpoint()
        );
    }

    public function testGetRelatedSelfReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/plugins/api',
            $this->resource->getRelatedSelf()
        );
    }

    public function testGetRelatedHypermediaReturnsSelfAndResourceUrls(): void
    {
        $expected = [
            'self' => 'http://localhost/api/plugins/api',
            'resource' => 'http://localhost/api/plugins/'
        ];

        $this->assertEquals(
            $expected,
            $this->resource->getRelatedHypermedia()
        );
    }

    public function testGetHypermediaReturnsOnlyRelatedHypermedia(): void
    {
        $expected = [
            'related' => [
                'self' => 'http://localhost/api/plugins/api',
                'resource' => 'http://localhost/api/plugins/'
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->getHypermedia()
        );
    }

    public function testToJsonReturnsResourceObject(): void
    {
        $expected = [
            'type' => Constants::TYPE_PLUGIN,
            'id' => 'api',
            'attributes' => $this->plugin->config(),
            'links' => [
                'related' => [
                    'self' => 'http://localhost/api/plugins/api',
                    'resource' => 'http://localhost/api/plugins/'
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
        $expected = $this->plugin->config();

        $this->assertEquals(
            $expected,
            $this->resource->toJson(true)
        );
    }
}
