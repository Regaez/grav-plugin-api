<?php
namespace GravApi\Config;

use GravApi\Helpers\ArrayHelper;

/**
 * Class Method
 * @package GravApi\Config
 */
class Method
{
    /**
     * @var boolean
     */
    protected $enabled = false;

    /**
     * @var boolean
     */
    protected $useAuth = true;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $ignore_files = array();

    public function __construct($config = null)
    {
        if (!isset($config) || !is_array($config)) {
            $config = array();
        }

        $enabled = isset($config['enabled'])
            ? $config['enabled']
            : null;

        if (is_bool($enabled)) {
            $this->enabled = $enabled;
        }

        $auth = isset($config['auth'])
            ? $config['auth']
            : null;

        if (is_bool($auth)) {
            $this->useAuth = $auth;
        }

        $fields = isset($config['fields'])
            ? $config['fields']
            : null;

        if (is_array($fields)) {
            $this->fields = ArrayHelper::asStringArray($fields);
        }

        $files = isset($config['ignore_files'])
            ? $config['ignore_files']
            : null;

        if (is_array($files)) {
            $this->ignore_files = ArrayHelper::asStringArray($files);
        }
    }

    public function __get(string $property)
    {
        // We only allow access to specific properties
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return null;
    }
}
