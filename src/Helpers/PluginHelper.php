<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;
use Grav\Common\Plugin;

/**
 * Class PluginHelper
 * @package GravApi\Helpers
 */
class PluginHelper
{
    /**
     * Finds a plugin by name
     *
     * @param string $name
     * @return Plugin|null
     */
    public static function find($name = '')
    {
        foreach (Grav::instance()['plugins'] as $plugin) {
            if ($plugin->name === $name) {
                return $plugin;
            }
        }

        return null;
    }
}
