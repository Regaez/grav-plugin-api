<?php
namespace GravApi\Resources;

use Grav\Common\Plugin;

/**
 * Class PluginCollectionResource
 * @package GravApi\Resources
 */
class PluginCollectionResource extends CollectionResource
{
    /**
     * @param Plugin[] $plugins
     */
    public function __construct($plugins)
    {
        $this->collection = $plugins;
    }

    /**
     * Accepts an resource from the collection and
     * returns a new PluginResource instance
     *
     * @param  Plugin $plugin
     * @return PluginResource
     */
    public function getResource($plugin)
    {
        return new PluginResource($plugin);
    }
}
