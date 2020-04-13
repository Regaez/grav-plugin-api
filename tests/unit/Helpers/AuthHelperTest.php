<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Helpers\AuthHelper;

final class AuthHelperTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();
    }

    public function testCheckRolesShouldReturnFalseWhenNullUserGiven(): void
    {
        $this->assertFalse(AuthHelper::checkRoles(null, []));
    }

    public function testCheckRolesShouldReturnFalseWhenUserLacksRole(): void
    {
        // Load user with no role
        $user = $this->grav['accounts']->load('joe');
        $user->authenticated = true;

        $this->assertFalse(AuthHelper::checkRoles($user, ['api.pages_read']));
    }

    public function testCheckRolesShouldReturnTrueWhenUserHasSuperRole(): void
    {
        // Load user with no role
        $user = $this->grav['accounts']->load('development');
        $user->authenticated = true;

        $this->assertTrue(AuthHelper::checkRoles($user, ['api.pages_read']));
    }

    public function testCheckRolesShouldReturnTrueWhenUserHasRole(): void
    {
        // Load user with no role
        $user = $this->grav['accounts']->load('percy');
        $user->authenticated = true;

        $this->assertTrue(AuthHelper::checkRoles($user, ['api.pages_read']));
    }

    public function testHasPageAccessShouldReturnTrueWhenUserHasRoute(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => [
                '/test'
            ]
        ]);
        $page = $this->grav['pages']->find('/test');

        $this->assertTrue(AuthHelper::hasPageAccess($user, $page, 'get'));
    }

    public function testHasPageAccessShouldReturnTrueWhenUserHasTaxonomy(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => [
                'category' => [
                    'blog'
                ]
            ]
        ]);
        $page = $this->grav['pages']->find('/test');

        $this->assertTrue(AuthHelper::hasPageAccess($user, $page, 'get'));
    }

    public function testHasPageAccessShouldReturnTrueWhenGroupHasRoute(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('groups', ['testers']);
        $page = $this->grav['pages']->find('/test');

        $this->assertTrue(AuthHelper::hasPageAccess($user, $page, 'get'));
    }

    public function testHasPageAccessShouldReturnTrueWhenGroupHasTaxonomy(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('groups', ['bloggers']);
        $page = $this->grav['pages']->find('/test');

        $this->assertTrue(AuthHelper::hasPageAccess($user, $page, 'get'));
    }

    public function testHasPageAccessShouldReturnFalseWhenNoPermissions(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('joe');
        $page = $this->grav['pages']->find('/test');

        $this->assertFalse(AuthHelper::hasPageAccess($user, $page, 'get'));
    }

    public function testGetUserRoutesShouldReturnArray(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => '/test'
        ]);

        $this->assertIsArray(AuthHelper::getUserRoutes($user, 'get'));
    }

    public function testGetUserRoutesShouldReturnEmptyArray(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', []);

        $this->assertEquals(0, count(AuthHelper::getUserRoutes($user, 'get')));
        // For null user, we also expect an empty array
        $this->assertEquals(0, count(AuthHelper::getUserRoutes(null, 'get')));
    }

    public function testGetUserRoutesShouldReturnUserData(): void
    {
        $data = ['/test'];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => $data
        ]);

        $this->assertEquals($data, AuthHelper::getUserRoutes($user, 'get'));
    }

    public function testGetUserTaxonomyShouldReturnArray(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => [
                'category' => [
                    'blog'
                ]
            ]
        ]);

        $this->assertIsArray(AuthHelper::getUserTaxonomy($user, 'get'));
    }

    public function testGetUserTaxonomyShouldReturnEmptyArray(): void
    {
        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', []);

        $this->assertEquals(0, count(AuthHelper::getUserTaxonomy($user, 'get')));
        // For null user, we also expect an empty array
        $this->assertEquals(0, count(AuthHelper::getUserTaxonomy(null, 'get')));
    }

    public function testGetUserTaxonomyShouldReturnUserData(): void
    {
        $data = [
            'category' => [
                'blog'
            ]
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => $data
        ]);

        $this->assertEquals($data, AuthHelper::getUserTaxonomy($user, 'get'));
    }

    public function testGetGroupRoutesShouldReturnArray(): void
    {
        $this->assertIsArray(AuthHelper::getGroupRoutes('testers', 'get'));
    }

    public function testGetGroupRoutesShouldReturnEmptyArray(): void
    {
        $this->assertEquals(0, count(AuthHelper::getGroupRoutes('siteadmin', 'get')));
    }

    public function testGetGroupRoutesShouldReturnUserData(): void
    {
        $data = ['/test'];

        $this->assertEquals($data, AuthHelper::getGroupRoutes('testers', 'get'));
    }

    public function testGetGroupTaxonomyShouldReturnArray(): void
    {
        $this->assertIsArray(AuthHelper::getGroupTaxonomy('bloggers', 'get'));
    }

    public function testGetGroupTaxonomyShouldReturnEmptyArray(): void
    {
        $this->assertEquals(0, count(AuthHelper::getGroupTaxonomy('siteadmin', 'get')));
    }

    public function testGetGroupTaxonomyShouldReturnUserData(): void
    {
        $data = [
            'category' => [
                'blog'
            ]
        ];

        $this->assertEquals($data, AuthHelper::getGroupTaxonomy('bloggers', 'get'));
    }

    public function testHasMatchingRouteShouldTrueForExactMatch(): void
    {
        $route = '/test';
        $routes = ['/test'];

        $this->assertTrue(AuthHelper::hasMatchingRoute($route, $routes));
    }

    public function testHasMatchingRouteShouldTrueForChild(): void
    {
        $route = '/test/child';
        $routes = ['/test/*'];

        $this->assertTrue(AuthHelper::hasMatchingRoute($route, $routes));
    }

    public function testHasMatchingRouteShouldTrueForDescendant(): void
    {
        $route = '/test/child/grandchild';
        $routes = ['/test/*'];

        $this->assertTrue(AuthHelper::hasMatchingRoute($route, $routes));
    }

    public function testHasMatchingRouteShouldFalseForParentOfDescendant(): void
    {
        $route = '/test';
        $routes = ['/test/*'];

        $this->assertFalse(AuthHelper::hasMatchingRoute($route, $routes));
    }

    public function testHasMatchingRouteShouldFalseForNoMatch(): void
    {
        $route = '/foo';
        $routes = ['/bar'];

        $this->assertFalse(AuthHelper::hasMatchingRoute($route, $routes));
    }

    public function testGetCollectionParamsShouldReturnPageSelfRoute(): void
    {
        $expect = ['@page.self' => '/test'];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/test']
        ]);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldReturnPageDescendantRoute(): void
    {
        $expect = ['@page.descendants' => '/test'];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/test/*']
        ]);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldRemoveDuplicateRoutes(): void
    {
        $expect = ['@page.self' => '/test'];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/test', '/test']
        ]);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldKeepUniqueRoutes(): void
    {
        $expect = [
            '@page.self' => '/test',
            '@page.descendants' => '/test'
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/test', '/test/*']
        ]);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldReturnTaxonomy(): void
    {
        $data = [
            'category' => [
                'blog'
            ]
        ];

        $expect = [
            '@taxonomy' => $data
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => $data
        ]);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldReturnGroupRoutes(): void
    {
        $expect = ['@page.self' => '/test'];

        // Load user with matching route
        $user = $this->grav['accounts']->load('joe');
        $user->set('groups', ['testers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldReturnGroupTaxonomy(): void
    {
        $data = [
            'category' => [
                'blog'
            ]
        ];

        $expect = [
            '@taxonomy' => $data
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('joe');
        $user->set('groups', ['bloggers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldMergeGroupAndUserRoutes(): void
    {
        $expect = [
            '@page.self' => '/test',
            '@page.self' => '/typography'
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/typography']
        ]);
        $user->set('groups', ['testers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldRemoveGroupAndUserDuplicateRoutes(): void
    {
        $expect = [
            '@page.self' => '/test'
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/test']
        ]);
        $user->set('groups', ['testers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldMergeGroupAndUserTaxonomy(): void
    {
        $taxonomy = [
            'category' => [
                'news'
            ],
            'tag' => [
                'grav'
            ]
        ];

        // We expect `category:news` and `tag:grav` from user and `category:blog` from group
        $expect = [
            '@taxonomy' => [
                'category' => [
                    'blog',
                    'news'
                ],
                'tag' => [
                    'grav'
                ]
            ]
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => $taxonomy
        ]);
        $user->set('groups', ['bloggers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldNotDuplicateGroupAndUserTaxonomy(): void
    {
        $taxonomy = [
            'category' => [
                'blog'
            ]
        ];

        // We do not expect `category` or `blog` to appear twice, even though both user and group have it.
        $expect = [
            '@taxonomy' => [
                'category' => [
                    'blog'
                ]
            ]
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('tom');
        $user->set('api.advanced_access.pages.get', [
            'taxonomy' => $taxonomy
        ]);
        $user->set('groups', ['bloggers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }

    public function testGetCollectionParamsShouldReturnAllFields(): void
    {
        $taxonomy = [
            'category' => [
                'blog',
                'news'
            ],
            'tag' => [
                'grav'
            ]
        ];

        // We check that all routes and taxonomy values are added/deduplicated
        $expect = [
            '@page.self' => '/test',
            '@page.self' => '/typography',
            '@page.descendants' => '/test',
            '@taxonomy' => [
                'category' => [
                    'blog',
                    'news'
                ],
                'tag' => [
                    'grav'
                ]
            ]
        ];

        // Load user with matching route
        $user = $this->grav['accounts']->load('joe');
        $user->set('api.advanced_access.pages.get', [
            'routes' => ['/typography', '/test/*'],
            'taxonomy' => $taxonomy
        ]);
        $user->set('groups', ['bloggers', 'testers']);

        $this->assertEquals($expect, AuthHelper::getCollectionParams($user));
    }
}
