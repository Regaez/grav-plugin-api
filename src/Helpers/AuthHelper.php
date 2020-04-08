<?php
namespace GravApi\Helpers;

use Grav\Common\User;

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
        foreach ($roles as $role) {
            if ($user->authorize($role)) {
                return true;
            }
        }

        return false;
    }
}
