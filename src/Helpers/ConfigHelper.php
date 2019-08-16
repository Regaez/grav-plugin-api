<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;
use Grav\Common\Config\ConfigFileFinder;
use GravApi\Models\ConfigModel;
use GravApi\Resources\ConfigCollectionResource;

/**
 * Class ConfigHelper
 * @package GravApi\Helpers
 */
class ConfigHelper
{
    /**
     * Returns an array of ConfigModels that can be passed to the ConfigCollectionResource
     *
     * @return ConfigModel[]
     */
    public static function loadConfigs()
    {
        $configs = [];

        // Find all the root config files
        $location = Grav::instance()['locator']->findResources('config://');
        $configFiles = (new ConfigFileFinder)->listFiles($location);

        // Retrieve fields of each config file
        foreach ($configFiles as $name => $value) {
            $data = Grav::instance()['config']->get($name);
            if ($data) {
                $configs[] = new ConfigModel($name, $data);
            }
        }

        return $configs;
    }

    /**
     * Returns a ConfigModel for a given config name, or null if it doesn't exist or is on the filter list
     *
     * @param string $name
     * @return ConfigModel|null
     */
    public static function loadConfig(string $name)
    {
        $data = Grav::instance()['config']->get($name);

        // If the Config doesn't exist, OR it is present on the filter list
        // (i.e. we don't want to allow user access to it)
        if (!$data || in_array($name, ConfigCollectionResource::getFilter())) {
            return null;
        }

        return new ConfigModel($name, $data);
    }
}
