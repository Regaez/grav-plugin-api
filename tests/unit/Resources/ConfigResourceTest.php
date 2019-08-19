<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Models\ConfigModel;
use GravApi\Helpers\ConfigHelper;
use GravApi\Resources\ConfigResource;

final class ConfigResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var ConfigModel $config */
    protected $config;

    /** @var ConfigResource $resource */
    protected $resource;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $this->config = ConfigHelper::loadConfig('site');
        $this->resource = new ConfigResource($this->config);
    }

    public function testGetIdReturnsConfigName(): void
    {
        $this->assertEquals(
            'site',
            $this->resource->getId()
        );
    }

    public function testGetResourceTypeReturnsConfig(): void
    {
        $this->assertEquals(
            Constants::TYPE_CONFIG,
            $this->resource->getResourceType()
        );
    }

    public function testGetResourceAttributesReturnsConfigData(): void
    {
        $this->assertEquals(
            $this->config->data,
            $this->resource->getResourceAttributes()
        );
    }

    public function testGetResourceEndpointReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/configs/',
            $this->resource->getResourceEndpoint()
        );
    }

    public function testGetRelatedSelfReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/configs/site',
            $this->resource->getRelatedSelf()
        );
    }

    public function testGetRelatedHypermediaReturnsSelfAndResourceUrls(): void
    {
        $expected = [
            'self' => 'http://localhost/api/configs/site',
            'resource' => 'http://localhost/api/configs/'
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
                'self' => 'http://localhost/api/configs/site',
                'resource' => 'http://localhost/api/configs/'
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
            'type' => Constants::TYPE_CONFIG,
            'id' => 'site',
            'attributes' => $this->config->data,
            'links' => [
                'related' => [
                    'self' => 'http://localhost/api/configs/site',
                    'resource' => 'http://localhost/api/configs/'
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
        $expected = $this->config->data;

        $this->assertEquals(
            $expected,
            $this->resource->toJson(true)
        );
    }
}
