<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Helpers\ConfigHelper;
use GravApi\Resources\ConfigResource;
use GravApi\Resources\ConfigCollectionResource;

final class ConfigCollectionResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var ConfigCollectionResource $resource */
    protected $resource;

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

        $configs = ConfigHelper::loadConfigs();
        $this->resource = new ConfigCollectionResource($configs);
    }

    public function testGetResourceReturnsConfigResource(): void
    {
        $config = ConfigHelper::loadConfig('site');
        $this->assertInstanceOf(
            ConfigResource::class,
            $this->resource->getResource($config)
        );
    }

    public function testToJsonReturnsItemsArray(): void
    {
        $this->assertIsArray(
            $this->resource->toJson()['items']
        );
    }

    public function testToJsonDoesNotReturnSecurityConfig(): void
    {
        $configs = $this->resource->toJson()['items'];

        foreach ($configs as $config) {
            $this->assertNotEquals($config['id'], 'security');
        }
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
        $this->assertEquals(5, $result);
    }
}
