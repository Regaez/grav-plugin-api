<?php
namespace GravApi\Models;

/**
 * Class ConfigModel
 * @package GravApi\Models
 */
class ConfigModel
{
    /**
     * Returns the identifying name of the config
     *
     * @return string
     */
    protected $id;

    /**
     * Returns the data array for this config
     *
     * @return array
     */
    protected $data;

    public function __construct(string $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}
