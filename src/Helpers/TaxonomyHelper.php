<?php
namespace GravApi\Helpers;

use \Grav\Common\Grav;
use \Grav\Common\Uri;
use \Grav\Common\User\Interfaces\UserInterface;
use \GravApi\Helpers\AuthHelper;

/**
 * Class TaxonomyHelper
 * @package GravApi\Helpers
 */
class TaxonomyHelper
{
    /**
     * Merges two taxonomy arrays together, removing any duplicate values.
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function merge(array $a, array $b)
    {
        $result = array();

        foreach (array_merge_recursive($a, $b) as $key => $value) {
            $result[$key] = array_unique($value);
        }

        return $result;
    }

    /**
     * Intersects two taxonomy arrays, returning an array containing only the shared taxonomy values.
     *
     * @param array $a
     * @param array $b
     * @return array An empty array will be returned if there is no intersection.
     */
    public static function intersect(array $a, array $b)
    {
        $result = array();

        $matchingKeys = array_filter($a, function ($key) use ($b) {
            return array_key_exists($key, $b);
        }, ARRAY_FILTER_USE_KEY);


        foreach ($matchingKeys as $key => $value) {
            $intersect = array_intersect($value, $b[$key]);
            if (count($intersect) > 0) {
                $result[$key] = $intersect;
            }
        }

        return $result;
    }

    /**
     * Determines if two taxonomy arrays share any values
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    public static function hasIntersect($a, $b)
    {
        return count(self::intersect($a, $b)) > 0;
    }

    /**
     * Returns the API's advanced acess taxonomy field from the user's profile for a given HTTP method.
     *
     * This is a hacky workaround to populate the taxonomy field with data from the user's profile.
     * See https://github.com/getgrav/grav-plugin-admin/issues/1472
     *
     * @param string $method One of `get`, `post`, `patch`, `delete`
     * @return array An empty array will be return if no taxonomy data exists
     */
    public static function getUserTaxonomy($method)
    {
        $grav = Grav::instance();

        /** @var Uri */
        $uri = $grav['uri'];
        // We extract the username from the URL, e.g. /admin/user/tom
        $username = end($uri->paths());

        /** @var UserInterface */
        $user = $grav['accounts']->load($username);

        return AuthHelper::getUserTaxonomy($user, $method);
    }
}
