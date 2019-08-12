<?php
namespace GravApi\Resources;

use GravApi\Config\Config;

abstract class Resource
{
    protected $resource;

    /**
     * A list of fields to remove from the resource attributes response
     *
     * @var string[]
     */
    protected $filter = null;

    abstract public function getId();

    /**
     * Returns the hypermedia array for this resource
     *
     * @return array
     */
    abstract protected function getHypermedia();

    /**
    * Returns the attributes associated with this resource
    *
    * @return array
    */
    abstract protected function getResourceAttributes();

    /**
     * Returns the resource type
     *
     * @return string
     */
    abstract protected function getResourceType();

    /**
     * Returns the releated hypermedia array for this resource type
     *
     * @return array
     */
    public function getRelatedHypermedia()
    {
        return [
            'self' => $this->getRelatedSelf(),
            'resource' => $this->getResourceEndpoint()
        ];
    }

    /**
     * Returns the hypermedia URL for this resource
     *
     * @return string
     */
    public function getRelatedSelf()
    {
        return $this->getResourceEndpoint() . $this->getId();
    }

    /**
     * Returns the API endpoint for this resource type
     *
     * @return string
     */
    public function getResourceEndpoint()
    {
        return Config::instance()->getEndpoint(
            $this->getResourceType()
        );
    }

    /**
     * Returns the resource object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  bool         $attributesOnly optional
     * @return array
     */
    public function toJson($attributesOnly = false)
    {
        $attributes = $this->getResourceAttributes();

        if ($attributesOnly) {
            return $attributes;
        }

        return [
            'type' => $this->getResourceType(),
            'id' => $this->getId(),
            'attributes' => $attributes,
            'links' => $this->getHypermedia()
        ];
    }
}
