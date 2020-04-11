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
        /** @var Config */
        $config = Grav::instance()['config'];
        $groups = (array) $user->get('groups');

        foreach ($groups as $group) {
            // Check user's groups routes against page route
            $routes = $config->get("groups.{$group}.api.advanced_access.pages.{$method}.routes", []);

            $hasMatchingRoute = self::hasMatchingRoute(
                $page->route(),
                $routes
            );

            if ($hasMatchingRoute) {
                return true;
            }

            // Check user's groups against the page's taxonomies
            $taxonomies = $config->get("groups.{$group}.api.advanced_access.pages.{$method}.taxonomy", []);

            $hasTaxonomyIntersect = TaxonomyHelper::hasIntersect(
                $page->taxonomy(),
                $taxonomies
            );

            if ($hasTaxonomyIntersect) {
                return true;
            }
        }

        // Check user's routes against the page's route
        $hasMatchingRoute = self::hasMatchingRoute(
            $page->route(),
            $user->get("api.advanced_access.pages.{$method}.routes", [])
        );

        if ($hasMatchingRoute) {
            return true;
        }


        // Check user against the page's taxonomies
        $hasTaxonomyIntersect = TaxonomyHelper::hasIntersect(
            $page->taxonomy(),
            $user->get("api.advanced_access.pages.{$method}.taxonomy", [])
        );

        if ($hasTaxonomyIntersect) {
            return true;
        }

        return false;
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
    public static function hasMatchingRoute($route, $routes)
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
        $items = [];

        // Currently the function is only called by `getPages`, but could make this a parameter if necessary
        $method = Constants::METHOD_GET;

        /** @var Config */
        $config = Grav::instance()['config'];

        $groups = (array) $user->get('groups');

        $routes = [];
        $taxonomies = [];

        foreach ($groups as $group) {
            $groupRoutes = $config->get("groups.{$group}.api.advanced_access.pages.{$method}.routes", []);
            $routes = array_merge($routes, $groupRoutes);

            // Check user's groups against the page's taxonomies
            $taxonomies = TaxonomyHelper::merge(
                $taxonomies,
                $config->get("groups.{$group}.api.advanced_access.pages.{$method}.taxonomy", [])
            );
        }

        $routes = array_merge(
            $routes,
            $user->get("api.advanced_access.pages.{$method}.routes", [])
        );

        $taxonomies = TaxonomyHelper::merge(
            $taxonomies,
            $user->get("api.advanced_access.pages.{$method}.taxonomy", [])
        );

        foreach (array_unique($routes) as $r) {
            if (preg_match(Constants::REGEX_DESCENDANT_WILDCARD, $r)) {
                $items['@page.descendants'] = preg_replace(Constants::REGEX_DESCENDANT_WILDCARD, '', $r);
            } else {
                $items['@page.self'] = $r;
            }
        }

        if (count($taxonomies) > 0) {
            $items['@taxonomy'] = $taxonomies;
        }

        return $items;
    }
}
