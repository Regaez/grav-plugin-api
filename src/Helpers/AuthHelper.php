<?php
namespace GravApi\Helpers;

use Grav\Common\User;
use GravApi\Config\Constants;

/**
 * Class AuthHelper
 * @package GravApi\Helpers
 */
class AuthHelper
{
    /**
     * Checks whether a user has any of the required roles
     *
     * @param User $user
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
}
