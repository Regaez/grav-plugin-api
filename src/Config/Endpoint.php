<?php
namespace GravApi\Config;

use GravApi\Config\Constants;
use GravApi\Config\Method;

/**
 * Class Endpoint
 * @package GravApi\Config
 */
class Endpoint
{
    /**
     * @var Method
     */
    protected $get;

    /**
     * @var Method
     */
    protected $patch;

    /**
     * @var Method
     */
    protected $post;

    /**
     * @var Method
     */
    protected $delete;

    public function __construct($config = null)
    {
        if (!isset($config) || !is_array($config)) {
            $config = array();
        }

        $this->configureMethods($config);
    }

    protected function configureMethods(array $config)
    {
        foreach (Constants::METHODS as $method) {
            $params = isset($config[$method])
                ? $config[$method]
                : null;

            $property = $method;

            $this->{$property} = new Method($params);
        }
    }

    /**
     * @return Method|null
     */
    public function __get(string $method)
    {
        // Check if the desired method matches the available methods
        if (in_array($method, Constants::METHODS)) {
            return $this->{$method};
        }

        // If no key matches our methods
        return null;
    }
}
