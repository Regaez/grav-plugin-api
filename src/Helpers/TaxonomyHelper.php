<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;

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
        return sprintf(
            'api.taxonomy_%s_%s',
            strtolower(preg_replace('/\s+/', '_', $name)),
            strtolower(preg_replace('/\s+/', '_', $value))
        );
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
}
