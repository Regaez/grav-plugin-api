<?php
namespace GravApi\Resources;

use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Resources\Resource;

/**
 * Class ConfigResource
 * @package GravApi\Resources
 */
class ConfigResource extends Resource
{
    protected $id;

    public function __construct(object $config)
    {
        $this->id = $config->id;
        $this->resource = $config->data;

        // Set the attribute filter
        $this->setFilter();
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
     * @param array|null $fields
     * @return array
     */
    public function getResourceAttributes()
    {
        $attributes = $this->resource;

        // Filter for requested fields
        if ($this->filter) {
            $attributes = [];

            foreach ($this->filter as $field) {
                if (property_exists($this->config, $field)) {
                    $attributes[$field] = $this->config->{$field};
                }
            }
        }

        return $attributes;
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

    /**
     * Sets a filter for the list of attributes based on the
     * API plugin's config setting.

     * @return void
     */
    private function setFilter()
    {
        $filter = Config::instance()->configs->get['fields'];

        // TODO: improve validation of filter input
        if ($filter) {
            $this->filter = $filter;
        }
    }
}
