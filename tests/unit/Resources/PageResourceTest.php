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
            'endpoints' => [
                Constants::ENDPOINT_PAGE => [
                    Constants::METHOD_GET => [
                        'enabled' => true,
                        'fields' => $attributeFields
                    ]
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

    public function testGetResourceAttributesReturnsPageData(): void
    {
        $attributes = [
            "children" => [
                "/test/child",
                "/test/another-child"
            ],
            "childType" => "",
            "content" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "date" => 1565642012,
            "eTag" => false,
            "expires" => 604800,
            "exists" => true,
            "extension" => ".md",
            "extra" => [
                "header.custom_field" => "WORLD"
            ],
            "filePathClean" => "user/pages/test/default.md",
            "folder" => "test",
            "frontmatter" => "title: 'Test page'\ncustom_field: WORLD\ntaxonomy:\n    category: blog\n    tag: [news, grav]\n",
            "header" => [
                "title" => "Test page",
                "custom_field" => "WORLD",
                "taxonomy" => [
                    "category" => "blog",
                    "tag" => [
                        "news",
                        "grav"
                    ]
                ]
            ],
            "home" => false,
            "id" => "156564201272ebcd1270d0fccaa9958b7c3952fe87",
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
            "modified" => 1565642012,
            "modularTwig" => false,
            "modular" => false,
            "name" => "default.md",
            "order" => false,
            "orderDir" => "asc",
            "orderBy" => "default",
            "orderManual" => [],
            "permalink" => "http://localhost/test",
            "publishDate" => null,
            "published" => true,
            "raw" => "---\ntitle: 'Test page'\ncustom_field: WORLD\ntaxonomy:\n    category: blog\n    tag: [news, grav]\n---\n\n# Hello {{custom_field}}! This is a test.\n",
            "rawMarkdown" => "# Hello {{custom_field}}! This is a test.\n",
            "rawRoute" => "/test",
            "root" => false,
            "routable" => true,
            "route" => "/test",
            "routeCanonical" => "/test",
            "slug" => "test",
            "summary" => "<h1>Hello {{custom_field}}! This is a test.</h1>",
            "taxonomy" => [
                "category" => [
                    "blog"
                ],
                "tag" => [
                    "news",
                    "grav"
                ]
            ],
            "template" => "default",
            "title" => "Test page",
            "translatedLanguages" => [],
            "unpublishDate" => null,
            "untranslatedLanguages" => [],
            "visible" => false
        ];

        $result = $this->resource->getResourceAttributes();

        $this->assertEquals(
            $attributes['children'],
            $result['children']
        );
        $this->assertEquals(
            $attributes['childType'],
            $result['childType']
        );
        $this->assertEquals(
            $attributes['content'],
            $result['content']
        );
        // We check if int because this field constantly changes as file is touched
        $this->assertIsInt($result['date']);
        $this->assertEquals(
            $attributes['eTag'],
            $result['eTag']
        );
        $this->assertEquals(
            $attributes['expires'],
            $result['expires']
        );
        $this->assertEquals(
            $attributes['exists'],
            $result['exists']
        );
        $this->assertEquals(
            $attributes['extension'],
            $result['extension']
        );
        $this->assertEquals(
            $attributes['extra'],
            $result['extra']
        );
        $this->assertEquals(
            $attributes['filePathClean'],
            $result['filePathClean']
        );
        $this->assertEquals(
            $attributes['folder'],
            $result['folder']
        );
        $this->assertEquals(
            $attributes['header'],
            $result['header']
        );
        $this->assertEquals(
            $attributes['home'],
            $result['home']
        );
        // We check if string because this field constantly changes as file is touched
        $this->assertIsString($result['id']);
        $this->assertEquals(
            $attributes['isDir'],
            $result['isDir']
        );
        $this->assertEquals(
            $attributes['isFirst'],
            $result['isFirst']
        );
        $this->assertEquals(
            $attributes['isLast'],
            $result['isLast']
        );
        $this->assertEquals(
            $attributes['isPage'],
            $result['isPage']
        );
        $this->assertEquals(
            $attributes['language'],
            $result['language']
        );
        $this->assertEquals(
            $attributes['maxCount'],
            $result['maxCount']
        );
        $this->assertEquals(
            $attributes['media'],
            $result['media']
        );
        $this->assertEquals(
            $attributes['menu'],
            $result['menu']
        );
        $this->assertEquals(
            $attributes['metadata'],
            $result['metadata']
        );
        // We check if int because this field constantly changes as file is touched
        $this->assertIsInt($result['modified']);
        $this->assertEquals(
            $attributes['modularTwig'],
            $result['modularTwig']
        );
        $this->assertEquals(
            $attributes['modular'],
            $result['modular']
        );
        $this->assertEquals(
            $attributes['name'],
            $result['name']
        );
        $this->assertEquals(
            $attributes['order'],
            $result['order']
        );
        $this->assertEquals(
            $attributes['orderDir'],
            $result['orderDir']
        );
        $this->assertEquals(
            $attributes['orderBy'],
            $result['orderBy']
        );
        $this->assertEquals(
            $attributes['orderManual'],
            $result['orderManual']
        );
        $this->assertEquals(
            $attributes['permalink'],
            $result['permalink']
        );
        $this->assertEquals(
            $attributes['publishDate'],
            $result['publishDate']
        );
        $this->assertEquals(
            $attributes['published'],
            $result['published']
        );
        $this->assertEquals(
            $attributes['raw'],
            $result['raw']
        );
        $this->assertEquals(
            $attributes['rawMarkdown'],
            $result['rawMarkdown']
        );
        $this->assertEquals(
            $attributes['rawRoute'],
            $result['rawRoute']
        );
        $this->assertEquals(
            $attributes['root'],
            $result['root']
        );
        $this->assertEquals(
            $attributes['routable'],
            $result['routable']
        );
        $this->assertEquals(
            $attributes['route'],
            $result['route']
        );
        $this->assertEquals(
            $attributes['routeCanonical'],
            $result['routeCanonical']
        );
        $this->assertEquals(
            $attributes['slug'],
            $result['slug']
        );
        $this->assertEquals(
            $attributes['summary'],
            $result['summary']
        );
        $this->assertEquals(
            $attributes['taxonomy'],
            $result['taxonomy']
        );
        $this->assertEquals(
            $attributes['template'],
            $result['template']
        );
        $this->assertEquals(
            $attributes['title'],
            $result['title']
        );
        $this->assertEquals(
            $attributes['translatedLanguages'],
            $result['translatedLanguages']
        );
        $this->assertEquals(
            $attributes['unpublishDate'],
            $result['unpublishDate']
        );
        $this->assertEquals(
            $attributes['untranslatedLanguages'],
            $result['untranslatedLanguages']
        );
        $this->assertEquals(
            $attributes['visible'],
            $result['visible']
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
            'children' => [
                'http://localhost/api/pages/test/child',
                'http://localhost/api/pages/test/another-child'
            ],
            'parent' => null,
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
            'children' => [
                'http://localhost/test/child',
                'http://localhost/test/another-child'
            ],
            'parent' => null,
            'related' => [
                'self' => 'http://localhost/api/pages/test',
                'children' => [
                    'http://localhost/api/pages/test/child',
                    'http://localhost/api/pages/test/another-child'
                ],
                'parent' => null,
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
        $expected = [
            'type' => Constants::TYPE_PAGE,
            'id' => 'test',
            'attributes' => $this->resource->getResourceAttributes(),
            'links' => [
                'self' => 'http://localhost/test',
                'children' => [
                    'http://localhost/test/child',
                    'http://localhost/test/another-child'
                ],
                'parent' => null,
                'related' => [
                    'self' => 'http://localhost/api/pages/test',
                    'children' => [
                        'http://localhost/api/pages/test/child',
                        'http://localhost/api/pages/test/another-child'
                    ],
                    'parent' => null,
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
        $this->assertEquals(
            $this->resource->getResourceAttributes(),
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
                'children' => [
                    'http://localhost/test/child',
                    'http://localhost/test/another-child'
                ],
                'parent' => null,
                'related' => [
                    'self' => 'http://localhost/api/pages/test',
                    'children' => [
                        'http://localhost/api/pages/test/child',
                        'http://localhost/api/pages/test/another-child'
                    ],
                    'parent' => null,
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
