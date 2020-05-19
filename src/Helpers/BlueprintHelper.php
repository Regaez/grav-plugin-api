<?php
namespace GravApi\Helpers;

use \Grav\Common\Grav;
use \Grav\Common\User\Interfaces\UserInterface;
use \GravApi\Helpers\AuthHelper;

/**
 * Class BlueprintHelper
 * @package GravApi\Helpers
 */
class BlueprintHelper
{
    /**
     * Returns the API's advanced access taxonomy field from the user's profile for a given HTTP method.
     *
     * This is a hacky workaround to populate the taxonomy field with data from the user's profile.
     * See https://github.com/getgrav/grav-plugin-admin/issues/1472
     *
     * @param string $method One of `get`, `post`, `patch`, `delete`
     * @return array An empty array will be returned if no taxonomy data exists
     */
    public static function getUserTaxonomy($method)
    {
        $grav = Grav::instance();

        // We can pull the username from the admin route
        $username = $grav['admin']->route;

        /** @var UserInterface */
        $user = $grav['accounts']->load($username);

        return AuthHelper::getUserTaxonomy($user, $method);
    }
}
