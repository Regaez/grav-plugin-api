<?php
namespace GravApi\Resources;

use GravApi\Config\Constants;
use GravApi\Models\ConfigModel;
use GravApi\Resources\Resource;

/**
 * Class ConfigResource
 * @package GravApi\Resources
 */
class ConfigResource extends Resource
{
    protected $id;

    /**
     * @param ConfigModel $config
     * @return ConfigResource
     */
    public function __construct(ConfigModel $config)
    {
        $this->id = $config->id;
        $this->resource = $config->data;
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
        return $this->id;
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @return array
     */
    public function getResourceAttributes()
    {
        return $this->resource;
    }

    /**
     * Returns the resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return Constants::TYPE_CONFIG;
    }
}
