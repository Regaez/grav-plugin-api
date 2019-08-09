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
     * Returns the identifier for this resource
     *
     * @return string
     */
    public function getId()
    {
        return $this->resource->name;
    }

    /**
     * Returns the resource type
     *
     * @return string
     */
    protected function getResourceType()
    {
        return Constants::TYPE_PLUGIN;
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @param array|null $fields
     * @return array
     */
    protected function getResourceAttributes()
    {
        return (array) $this->resource->config();
    }

    /**
     * Returns the hypermedia array for this resource
     *
     * @return string
     */
    protected function getHypermedia()
    {
        return [
            'related' => $this->getRelatedHypermedia()
        ];
    }
}
