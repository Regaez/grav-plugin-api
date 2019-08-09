<?php
namespace GravApi\Resources;

use GravApi\Config\Config;

abstract class Resource
{
    protected $resource;

    abstract public function getId();
    abstract protected function getResourceType();

    /**
     * Returns the hypermedia array for this resource
     *
     * @return array
     */
    abstract protected function getHypermedia();

    /**
    * Returns the attributes associated with this resource
    *
    * @param array|null $fields optional
    * @return array
    */
    abstract protected function getResourceAttributes($fields);

    /**
     * Returns the API endpoint for this resource type
     *
     * @return string
     */
    protected function getResourceEndpoint()
    {
        return Config::instance()->getEndpoint(
            $this->getResourceType()
        );
    }

    /**
     * Returns the releated hypermedia array for this resource type
     *
     * @return array
     */
    protected function getRelatedHypermedia()
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
    protected function getRelatedSelf()
    {
        return $this->getResourceEndpoint() . $this->getId();
    }

    /**
     * Returns the resource object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  array|null   $fields         optional
     * @param  bool         $attributesOnly optional
     * @return array
     */
    public function toJson($fields = null, $attributesOnly = false)
    {
        $attributes = $this->getResourceAttributes($fields);

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
