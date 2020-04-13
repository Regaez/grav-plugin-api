<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;
use Grav\Common\Config\Config;
use Grav\Common\User\Interfaces\UserInterface;
use Grav\Common\Page\Interfaces\PageInterface;
use GravApi\Config\Constants;
use GravApi\Helpers\TaxonomyHelper;

/**
 * Class AuthHelper
 * @package GravApi\Helpers
 */
class AuthHelper
{
    /**
     * Checks whether a user has any of the required roles
     *
     * @param UserInterface $user
     * @param string[] $roles
     * @return bool
     */
    public static function checkRoles($user, $roles)
    {
        if (!$user) {
            return false;
        }

        // By default, the super role will always be allowed
        $allRoles = array_merge([Constants::ROLE_SUPER], $roles);

        foreach ($allRoles as $role) {
            if ($user->authorize($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines whether a user has access to a given page, based on the route or taxonomy values.
     *
     * @param UserInterface $user
     * @param PageInterface $page
     * @param string $method
     * @return bool
     */
    public static function hasPageAccess($user, $page, $method)
    {
        if (!$user) {
            return false;
        }

        // First we check inherited permissions from the user's groups
        foreach ((array) $user->get('groups') as $group) {
            // Check user's group routes against page route
            $hasMatchingRoute = self::hasMatchingRoute(
                $page->route(),
                self::getGroupRoutes($group, $method)
            );

            if ($hasMatchingRoute) {
                return true;
            }

            // Check user's group taxonomy against the page's taxonomies
            $hasTaxonomyIntersect = TaxonomyHelper::hasIntersect(
                $page->taxonomy(),
                self::getGroupTaxonomy($group, $method)
            );

            if ($hasTaxonomyIntersect) {
                return true;
            }
        }

        // Check user's routes against the page's route
        $hasMatchingRoute = self::hasMatchingRoute(
            $page->route(),
            self::getUserRoutes($user, $method)
        );

        if ($hasMatchingRoute) {
            return true;
        }

        // Check user against the page's taxonomies
        $hasTaxonomyIntersect = TaxonomyHelper::hasIntersect(
            $page->taxonomy(),
            self::getUserTaxonomy($user, $method)
        );

        if ($hasTaxonomyIntersect) {
            return true;
        }

        return false;
    }

    /**
     * Returns an array of routes this user can access.
     *
     * @param UserInterface $user
     * @param string $method The HTTP method
     * @return string[] If no routes configured, an empty array will be returned.
     */
    public static function getUserRoutes($user, $method)
    {
        if (!$user) {
            return array();
        }

        return (array) $user->get("api.advanced_access.pages.{$method}.routes", []);
    }

    /**
     * Returns an array of taxonomies this user can access.
     *
     * @param UserInterface $user
     * @param string $method The HTTP method
     * @return array If no taxonomies are configured, an empty array will be returned.
     */
    public static function getUserTaxonomy($user, $method)
    {
        if (!$user) {
            return array();
        }

        return (array) $user->get("api.advanced_access.pages.{$method}.taxonomy", []);
    }

    /**
     * Returns an array of routes this group can access.
     *
     * @param string $group The name of the group
     * @param string $method The HTTP method
     * @return string[] If no routes are configured, an empty array will be returned.
     */
    public static function getGroupRoutes($group, $method)
    {
        /** @var Config */
        $config = Grav::instance()['config'];

        return (array) $config->get("groups.{$group}.api.advanced_access.pages.{$method}.routes", []);
    }

    /**
     * Returns an array of taxonomies this user can access.
     *
     * @param string $group The name of the group
     * @param string $method The HTTP method
     * @return array If no taxonomies are configured, an empty array will be returned.
     */
    public static function getGroupTaxonomy($group, $method)
    {
        /** @var Config */
        $config = Grav::instance()['config'];

        return (array) $config->get("groups.{$group}.api.advanced_access.pages.{$method}.taxonomy", []);
    }

    /**
     * Determines whether a page route matches a value from an array of routes.
     *
     * The array could include routes with the "any descendant" wildcard.
     * For example, `/blog/*`, which would return true for a route such as `/blog/child`.
     *
     * @param string $route
     * @param string[] $routes An array of accessible routes
     * @return bool
     */
    public static function hasMatchingRoute($route, array $routes)
    {
        foreach ($routes as $r) {
            // Check the route for the "any descendent" wildcard
            if (preg_match(Constants::REGEX_DESCENDANT_WILDCARD, $r)) {
                // Replacing with a slash prevents matching on the exact $route, so we will only match descendents
                $ancestor = preg_replace(Constants::REGEX_DESCENDANT_WILDCARD, '/', $r);

                // Check if the route starts with the ancestor route
                if (strpos($route, $ancestor) === 0) {
                    return true;
                }
            } elseif ($route === $r) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the user's advanced access permissions as Collection parameters.
     *
     * @param UserInterface $user
     * @param string $method
     * @return array
     */
    public static function getCollectionParams($user)
    {
        if (!$user) {
            return array();
        }

        $items = [];
        $routes = [];
        $taxonomies = [];

        // Currently the function is only called by `getPages`, but could make this a parameter if necessary
        $method = Constants::METHOD_GET;

        // Gather user's group permissions
        foreach ((array) $user->get('groups') as $group) {
            // Get routes from user's group
            $groupRoutes = self::getGroupRoutes($group, $method);
            $routes = array_merge($routes, $groupRoutes);

            // Get taxonomies from user's group
            $taxonomies = TaxonomyHelper::merge(
                $taxonomies,
                self::getGroupTaxonomy($group, $method)
            );
        }

        // Gather user's individual permissions
        $routes = array_merge(
            $routes,
            self::getUserRoutes($user, $method)
        );

        $taxonomies = TaxonomyHelper::merge(
            $taxonomies,
            self::getUserTaxonomy($user, $method)
        );

        // Convert routes to Collection params items value
        foreach (array_unique($routes) as $r) {
            if (preg_match(Constants::REGEX_DESCENDANT_WILDCARD, $r)) {
                $items['@page.descendants'] = preg_replace(Constants::REGEX_DESCENDANT_WILDCARD, '', $r);
            } else {
                $items['@page.self'] = $r;
            }
        }

        // Add any taxonomy values as Collection items param
        if (count($taxonomies) > 0) {
            $items['@taxonomy'] = $taxonomies;
        }

        return $items;
    }
}
