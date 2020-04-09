<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;
use Grav\Common\User;

/**
 * Class TaxonomyHelper
 * @package GravApi\Helpers
 */
class TaxonomyHelper
{
    /**
     * Returns all possible taxonomy access roles based on
     * actual site taxonomy data
     *
     * @return string[]
     */
    public static function getRoles()
    {
        $roles = [];
        $taxonomies = Grav::instance()['taxonomy']->taxonomy();

        foreach ($taxonomies as $name => $value) {
            foreach ($value as $key => $v) {
                $roles[] = self::formatAsRole($name, $key);
            }
        }

        return $roles;
    }

    /**
     * Formats a taxonomy as an access role string
     *
     * @param string $name The name of the taxonomy
     * @param string $value The value of the taxonomy
     * @return string role
     */
    public function formatAsRole($name, $value)
    {
        return sprintf('api.taxonomy_%s___%s', $name, $value);
    }

    /**
     * Gets all access roles based on a single page's
     * taxonomy data
     *
     * @param Page $page
     * @return string[] $roles
     */
    public static function getPageRoles($page)
    {
        $roles = [];

        foreach ($page->taxonomy() as $name => $value) {
            foreach ($value as $v) {
                $roles[] = self::formatAsRole($name, $v);
            }
        }

        return $roles;
    }

    /**
    * Gets all taxonomy access roles posssessed by a user
    *
    * @param User $user
    * @return array $taxonomies
    */
    public static function getUserRolesAsTaxonomy($user)
    {
        $taxonomies = [];

        $roles = $user->get('access.api');

        foreach ($roles as $name => $value) {
            if (preg_match('/^taxonomy_(.*)___(.*)/', $name, $matches)) {
                $taxonomies[$matches[1]][] = $matches[2];
            }
        }

        return $taxonomies;
    }
}
