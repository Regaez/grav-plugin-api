<?php
namespace GravApi\Resources;

/**
 * Class ConfigResource
 * @package GravApi\Resources
 */
class ConfigResource
{
    protected $config;

    public function __construct($config)
    {
        $this->config = (object) $config;
    }

    /**
     * Returns the plugin object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  [array] $fields optional
     * @return [array]
     */
    public function toJson($fields = null)
    {
        // Filter for requested fields
        if ( $fields ) {
            $data = [];

            foreach ($fields as $field) {
                if ( property_exists($this->plugin, $field) ) {
                    $data[$field] = $this->plugin->{$field};
                }
            }

            return $data;
        }

        // Otherwise return everything
        return (array) $this->config;
    }
}
