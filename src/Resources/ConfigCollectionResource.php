<?php
namespace GravApi\Resources;

use GravApi\Config\Config;
use GravApi\Models\ConfigModel;
use GravApi\Resources\ConfigResource;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class ConfigCollectionResource extends CollectionResource
{
    /**
     * @param ConfigModel[] $configs
     * @return ConfigCollectionResource
     */
    public function __construct($configs)
    {
        $this->collection = $configs;
        $this->filter = self::getFilter();
    }

    /**
     * Returns array of item IDs which should be filtered from this CollectionResource
     *
     * @return array
     */
    public static function getFilter()
    {
        // We don't want to show anyone our security settings!
        $filter = ['security'];
        $ignore_files = [];

        // Check the config for any config files we should ignore in addition
        // to the resource's filter list
        if (Config::instance()->configs) {
            $ignore_files = Config::instance()->configs->ignore_files;
        }

        if (!empty($ignore_files) && is_array($ignore_files)) {
            return array_merge($filter, $ignore_files);
        }

        // Otherwise we just return our default resource filter
        return $filter;
    }

    /**
     * Accepts an resource from the collection and
     * returns a new ConfigResource instance
     *
     * @param  ConfigModel $config
     * @return ConfigResource
     */
    public function getResource($config)
    {
        return new ConfigResource($config);
    }
}
