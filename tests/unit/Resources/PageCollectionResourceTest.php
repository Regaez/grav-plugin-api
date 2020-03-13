<?php

declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Resources\PageResource;
use GravApi\Resources\PageCollectionResource;
use GravApi\Config\Config;

final class PageCollectionResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var PageCollectionResource $resource */
    protected $resource;

    protected $route = '/test';

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance();

        $pages = $this->grav['pages']->all();
        $this->resource = new PageCollectionResource($pages);
    }

    public function testGetResourceReturnsPageResource(): void
    {
        $page = $this->grav['pages']->find($this->route);
        $this->assertInstanceOf(
            PageResource::class,
            $this->resource->getResource($page)
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
        $this->assertEquals(6, $result);
    }
}
