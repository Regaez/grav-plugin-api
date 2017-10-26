<?php
namespace GravApi\Resources;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class ConfigCollectionResource
{
    protected $configs;
    protected $filter;

    public function __construct($configs)
    {
        $this->configs = (object) $configs;

        // We don't want to show anyone our security settings!
        $this->filter = array('security');
    }

    public function toJson($filter = array())
    {
        $data = [];

        foreach ($this->configs as $name => $config) {
            // Skip if file is in either resource's filter or user custom filter
            if (in_array($name, $this->filter) || in_array($name, $filter)) {
                continue;
            }

            $data[$name] = $config;
        }

        return $data;
    }
}
