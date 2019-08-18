<?php
namespace GravApi\Config;

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
    protected $useAuth = false;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $ignore_files = array();

    public function __construct(array $config = array())
    {
        if (isset($config['enabled'])) {
            $this->enabled = $config['enabled'];
        }

        if (isset($config['auth'])) {
            $this->auth = $config['auth'];
        }

        if (isset($config['fields'])) {
            // TODO: field string array validation
            $this->fields = $config['fields'];
        }

        if (isset($config['ignore_files'])) {
            // TODO: ignore_files string array validation
            $this->ignore_files = $config['ignore_files'];
        }
    }

    /**
     * Returns whether or not this endpoint/method is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns whether or not this endpoint/method is enabled
     *
     * @return boolean
     */
    public function useAuth()
    {
        return $this->useAuth;
    }

    /**
     * Returns whether or not this field should be filtered
     *
     * @return boolean
     */
    public function shouldFilterField(string $field)
    {
        return in_array($field, $this->fields);
    }

    /**
     * Returns whether or not this files should be ignored
     *
     * @return boolean
     */
    public function shouldIgnoreFile(string $file)
    {
        return in_array($file, $this->ignore_files);
    }
}
