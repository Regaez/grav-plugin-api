<?php
namespace GravApi\Resources;

/**
 * Class ConfigResource
 * @package GravApi\Resources
 */
class ConfigResource
{
    protected $config;
    protected $id;

    public function __construct($config)
    {
        // we should only ever have a single item array
        foreach ($config as $key => $value) {
            $this->id = $key;
        }

        $this->config = (object) $config[$this->id];
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
        $attributes = (array) $this->config;

        // Filter for requested fields
        if ($fields) {
            $attributes = [];

            foreach ($fields as $field) {
                if (property_exists($this->config, $field)) {
                    $attributes[$field] = $this->config->{$field};
                }
            }
        }

        // Return Resource object
        return [
            'type' => 'config',
            'id' => $this->id,
            'attributes' => $attributes
        ];
    }
}
