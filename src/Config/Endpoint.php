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
     * @var boolean
     */
    protected $isGeneric = true;

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

    public function __construct(array $config = array())
    {
        // If the config contains any keys which match methods
        if (count(array_intersect_key(Constants::METHODS, $config)) > 0) {
            $this->configureMethods($config);
            $this->isGeneric = false;
        } else {
            // otherwise we create a generic endpoint handler and store it in the get object
            $this->get = new Method($config);
        }
    }

    protected function configureMethods(array $config)
    {
        $this->get = new Method($config[Constants::METHOD_GET]);
        $this->patch = new Method($config[Constants::METHOD_PATCH]);
        $this->post = new Method($config[Constants::METHOD_POST]);
        $this->delete = new Method($config[Constants::METHOD_DELETE]);
    }

    /**
     * @return Method|null
     */
    public function __get(string $method)
    {
        // If our Endpoint is generic, i.e. doesn't specify config per method
        // then we return the generic Method config
        if ($this->isGeneric) {
            return $this->get;
        }

        // Check if the desired method matches the available methods
        if (array_key_exists($method, Constants::METHODS)) {
            return $this->{$method};
        }

        // If no key matches our methods
        return null;
    }
}
