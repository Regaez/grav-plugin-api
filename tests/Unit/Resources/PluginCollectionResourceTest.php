<?php

declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Resources\PluginResource;
use GravApi\Resources\PluginCollectionResource;
use GravApi\Config\Config;

final class PluginCollectionResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var PluginCollectionResource $resource */
    protected $resource;

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

        $plugins = $this->grav['plugins'];
        $this->resource = new PluginCollectionResource($plugins);
    }

    public function testGetResourceReturnsPluginResource(): void
    {
        $plugin = $this->grav['plugins']->current();
        $this->assertInstanceOf(
            PluginResource::class,
            $this->resource->getResource($plugin)
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
        $this->assertEquals(8, $result);
    }
}
