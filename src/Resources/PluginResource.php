<?php
namespace GravApi\Resources;

use GravApi\Resources\Resource;
use Grav\Common\Plugin;
use GravApi\Config\Constants;

/**
 * Class PluginResource
 * @package GravApi\Resources
 */
class PluginResource extends Resource
{
    public function __construct(Plugin $plugin)
    {
        $this->resource = $plugin;
    }

    /**
     * Returns the identifier for this resource
     *
     * @return [string]
     */
    public function getId()
    {
        return $this->resource->name;
    }

    /**
     * Returns the resource type
     *
     * @return [string]
     */
    protected function getResourceType()
    {
        return Constants::TYPE_PLUGIN;
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @return [array]
     */
    protected function getResourceAttributes()
    {
        return (array) $this->resource->config();
    }
}
