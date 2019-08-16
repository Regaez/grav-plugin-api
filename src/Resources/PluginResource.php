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
    /**
     * @var Plugin
     */
    protected $resource;

    public function __construct(Plugin $plugin)
    {
        $this->resource = $plugin;
    }

    /**
     * Returns the hypermedia array for this resource
     *
     * @return string
     */
    public function getHypermedia()
    {
        return [
            'related' => $this->getRelatedHypermedia()
        ];
    }

    /**
     * Returns the identifier for this resource
     *
     * @return string
     */
    public function getId()
    {
        return $this->resource->name;
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @param array|null $fields
     * @return array
     */
    public function getResourceAttributes()
    {
        return (array) $this->resource->config();
    }

    /**
     * Returns the resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return Constants::TYPE_PLUGIN;
    }
}
