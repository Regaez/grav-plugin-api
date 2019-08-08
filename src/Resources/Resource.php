<?php
namespace GravApi\Resources;

use GravApi\Config\Config;

abstract class Resource
{
    protected $resource;

    abstract public function getId();
    abstract protected function getResourceType();
    abstract protected function getResourceAttributes();

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
     * @param  array $fields optional
     * @return array
     */
    public function toJson()
    {
        // TODO: filter attributes based on field param

        return [
            'type' => $this->getResourceType(),
            'id' => $this->getId(),
            'attributes' => $this->getResourceAttributes(),
            // TODO: improve hypermedia linking
            'links' => [
                'related' => [
                    'self' => $this->getRelatedSelf()
                ]
            ]
        ];
    }
}
