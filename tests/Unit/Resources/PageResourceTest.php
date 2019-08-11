<?php

declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use Grav\Common\Page;
use GravApi\Api;
use GravApi\Resources\PageResource;
use GravApi\Config\Constants;
use GravApi\Config\Config;

final class PageResourceTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    /** @var Page $page */
    protected $page;

    /** @var PageResource $resource */
    protected $resource;

    /** @var Api $api */
    protected $api;

    protected $id = 'test';

    protected function _before($attributeFields = array())
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();

        Config::instance([
            'api' => [
                'route' => 'api',
                'permalink' => 'http://localhost/api',
            ],
            'pages' => [
                'get' => [
                    'enabled' => true,
                    'fields' => $attributeFields
                ]
            ]
        ]);

        foreach ($this->grav['pages']->all() as $page) {
            if ($page->slug() === $this->id) {
                $this->page = $page;
                $this->resource = new PageResource($page);
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

    public function testGetResourceTypeReturnsPage(): void
    {
        $this->assertEquals(
            Constants::TYPE_PAGE,
            $this->resource->getResourceType()
        );
    }

    /**
     * TODO: https://github.com/Regaez/grav-plugin-api/issues/36
     *
     * Figure out why the `extra` field causes an error running tests
     */
    public function testGetResourceAttributesReturnsPageData(): void
    {
        $attributes = [
            "children" => [],
            "childType" => "",
            "content" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "date" => 1565545434,
            "eTag" => false,
            "expires" => 604800,
            "exists" => true,
            "extension" => ".md",
            // "extra" => [
            //     "header.custom_field" => "WORLD"
            // ],
            "filePathClean" => "user/pages/test/default.md",
            "folder" => "test",
            "frontmatter" => "title: 'Test page'\ncustom_field: WORLD",
            "header" => [
                "title" => "Test page",
                "custom_field" => "WORLD"
            ],
            "home" => false,
            "id" => "156554543472ebcd1270d0fccaa9958b7c3952fe87",
            "isDir" => false,
            "isFirst" => false,
            "isLast" => false,
            "isPage" => true,
            "language" => null,
            "lastModified" => false,
            "maxCount" => 20,
            "media" => [],
            "menu" => "Test page",
            "metadata" => [
                "generator" => [
                    "content" => "GravCMS",
                    "name" => "generator"
                ],
                "description" => [
                    "content" => "Grav is an easy to use, yet powerful, open source flat-file CMS",
                    "name" => "description"
                ]
            ],
            "modified" => 1565545434,
            "modularTwig" => false,
            "modular" => false,
            "name" => "default.md",
            "order" => false,
            "orderDir" => "asc",
            "orderBy" => "default",
            "orderManual" => [],
            "parent" => null,
            "permalink" => "http://localhost/test",
            "publishDate" => null,
            "published" => true,
            "raw" => "---\ntitle: 'Test page'\ncustom_field: WORLD\n---\n\n# Hello {{custom_field}}! This is a test.\n",
            "rawMarkdown" => "# Hello {{custom_field}}! This is a test.\n",
            "rawRoute" => "/test",
            "root" => false,
            "routable" => true,
            "route" => "/test",
            "routeCanonical" => "/test",
            "slug" => "test",
            "summary" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "taxonomy" => [],
            "template" => "default",
            "title" => "Test page",
            "translatedLanguages" => [],
            "unpublishDate" => null,
            "untranslatedLanguages" => [],
            "visible" => false
        ];

        $this->assertEquals(
            $attributes,
            $this->resource->getResourceAttributes()
        );
    }

    public function testGetResourceAttributesReturnsPageDataAgain(): void
    {
        $this->_before(['title', 'rawMarkdown', 'slug']);

        $attributes = [
            'title' => 'Test page',
            'rawMarkdown' => "# Hello {{custom_field}}! This is a test.\n",
            'slug' => 'test'
        ];

        $this->assertEquals(
            $attributes,
            $this->resource->getResourceAttributes()
        );
    }

    public function testGetResourceEndpointReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/pages/',
            $this->resource->getResourceEndpoint()
        );
    }

    public function testGetRelatedSelfReturnsExpectedUrl(): void
    {
        $this->assertEquals(
            'http://localhost/api/pages/test',
            $this->resource->getRelatedSelf()
        );
    }

    public function testGetRelatedHypermediaReturnsSelfAndResourceUrls(): void
    {
        $expected = [
            'self' => 'http://localhost/api/pages/test',
            'resource' => 'http://localhost/api/pages/'
        ];

        $this->assertEquals(
            $expected,
            $this->resource->getRelatedHypermedia()
        );
    }

    public function testGetHypermediaReturnsSelfAndRelatedHypermedia(): void
    {
        $expected = [
            'self' => 'http://localhost/test',
            'related' => [
                'self' => 'http://localhost/api/pages/test',
                'resource' => 'http://localhost/api/pages/'
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
            "children" => [],
            "childType" => "",
            "content" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "date" => 1565545434,
            "eTag" => false,
            "expires" => 604800,
            "exists" => true,
            "extension" => ".md",
            // "extra" => [
            //     "header.custom_field" => "WORLD"
            // ],
            "filePathClean" => "user/pages/test/default.md",
            "folder" => "test",
            "frontmatter" => "title: 'Test page'\ncustom_field: WORLD",
            "header" => [
                "title" => "Test page",
                "custom_field" => "WORLD"
            ],
            "home" => false,
            "id" => "156554543472ebcd1270d0fccaa9958b7c3952fe87",
            "isDir" => false,
            "isFirst" => false,
            "isLast" => false,
            "isPage" => true,
            "language" => null,
            "lastModified" => false,
            "maxCount" => 20,
            "media" => [],
            "menu" => "Test page",
            "metadata" => [
                "generator" => [
                    "content" => "GravCMS",
                    "name" => "generator"
                ],
                "description" => [
                    "content" => "Grav is an easy to use, yet powerful, open source flat-file CMS",
                    "name" => "description"
                ]
            ],
            "modified" => 1565545434,
            "modularTwig" => false,
            "modular" => false,
            "name" => "default.md",
            "order" => false,
            "orderDir" => "asc",
            "orderBy" => "default",
            "orderManual" => [],
            "parent" => null,
            "permalink" => "http://localhost/test",
            "publishDate" => null,
            "published" => true,
            "raw" => "---\ntitle: 'Test page'\ncustom_field: WORLD\n---\n\n# Hello {{custom_field}}! This is a test.\n",
            "rawMarkdown" => "# Hello {{custom_field}}! This is a test.\n",
            "rawRoute" => "/test",
            "root" => false,
            "routable" => true,
            "route" => "/test",
            "routeCanonical" => "/test",
            "slug" => "test",
            "summary" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "taxonomy" => [],
            "template" => "default",
            "title" => "Test page",
            "translatedLanguages" => [],
            "unpublishDate" => null,
            "untranslatedLanguages" => [],
            "visible" => false
        ];

        $expected = [
            'type' => Constants::TYPE_PAGE,
            'id' => 'test',
            'attributes' => $attributes,
            'links' => [
                'self' => 'http://localhost/test',
                'related' => [
                    'self' => 'http://localhost/api/pages/test',
                    'resource' => 'http://localhost/api/pages/'
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
            "children" => [],
            "childType" => "",
            "content" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "date" => 1565545434,
            "eTag" => false,
            "expires" => 604800,
            "exists" => true,
            "extension" => ".md",
            // "extra" => [
            //     "header.custom_field" => "WORLD"
            // ],
            "filePathClean" => "user/pages/test/default.md",
            "folder" => "test",
            "frontmatter" => "title: 'Test page'\ncustom_field: WORLD",
            "header" => [
                "title" => "Test page",
                "custom_field" => "WORLD"
            ],
            "home" => false,
            "id" => "156554543472ebcd1270d0fccaa9958b7c3952fe87",
            "isDir" => false,
            "isFirst" => false,
            "isLast" => false,
            "isPage" => true,
            "language" => null,
            "lastModified" => false,
            "maxCount" => 20,
            "media" => [],
            "menu" => "Test page",
            "metadata" => [
                "generator" => [
                    "content" => "GravCMS",
                    "name" => "generator"
                ],
                "description" => [
                    "content" => "Grav is an easy to use, yet powerful, open source flat-file CMS",
                    "name" => "description"
                ]
            ],
            "modified" => 1565545434,
            "modularTwig" => false,
            "modular" => false,
            "name" => "default.md",
            "order" => false,
            "orderDir" => "asc",
            "orderBy" => "default",
            "orderManual" => [],
            "parent" => null,
            "permalink" => "http://localhost/test",
            "publishDate" => null,
            "published" => true,
            "raw" => "---\ntitle: 'Test page'\ncustom_field: WORLD\n---\n\n# Hello {{custom_field}}! This is a test.\n",
            "rawMarkdown" => "# Hello {{custom_field}}! This is a test.\n",
            "rawRoute" => "/test",
            "root" => false,
            "routable" => true,
            "route" => "/test",
            "routeCanonical" => "/test",
            "slug" => "test",
            "summary" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "taxonomy" => [],
            "template" => "default",
            "title" => "Test page",
            "translatedLanguages" => [],
            "unpublishDate" => null,
            "untranslatedLanguages" => [],
            "visible" => false
        ];

        $this->assertEquals(
            $expected,
            $this->resource->toJson(true)
        );
    }

    public function testSetFilterReturnsSpecificResourceFields(): void
    {
        $this->_before(['title', 'rawMarkdown', 'slug']);

        $attributes = [
            'title' => 'Test page',
            'rawMarkdown' => "# Hello {{custom_field}}! This is a test.\n",
            'slug' => 'test'
        ];

        $expected = [
            'type' => Constants::TYPE_PAGE,
            'id' => 'test',
            'attributes' => $attributes,
            'links' => [
                'self' => 'http://localhost/test',
                'related' => [
                    'self' => 'http://localhost/api/pages/test',
                    'resource' => 'http://localhost/api/pages/'
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->resource->toJson()
        );
    }
}
