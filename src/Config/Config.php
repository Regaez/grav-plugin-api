<?php
namespace GravApi\Config;

use GravApi\Config\Constants;

/**
 * Class Config
 * @package GravApi\Config
 */
class Config
{

    private static $instance = null;

    private $api;
    private $pages;
    private $users;
    private $plugins;
    private $configs;

    /**
     * We map all settings to existing class properties
     * @param [array] $settings
     */
    private function __construct($settings = array())
    {
        foreach ($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = !empty($value)
                    ? (object) $value
                    : null;
            }
        }
    }

    public static function instance($settings = array())
    {
        // Check if instance is already exists
        if (self::$instance == null) {
            self::$instance = new Config($settings);
        }

        // Recreate config instance if new settings are passed
        if (!empty($settings)) {
            self::$instance = new Config($settings);
        }

        return self::$instance;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * Accepts one of the Constants::TYPE_* as a parameter,
     * and returns the matching, fully-qualified endpoint
     * for the given Resource Type.
     *
     * @param  [string] $resourceType e.g. Constants::TYPE_PAGE
     * @return [string] e.g. https://www.example.com/api/pages/
     */
    public function getEndpoint(string $resourceType)
    {
        $endpoint = '';

        switch ($resourceType) {
            case Constants::TYPE_PAGE:
                $endpoint = Constants::ENDPOINT_PAGE;
                break;
            case Constants::TYPE_USER:
                $endpoint = Constants::ENDPOINT_USER;
                break;
            case Constants::TYPE_PLUGIN:
                $endpoint = Constants::ENDPOINT_PLUGIN;
                break;
            case Constants::TYPE_CONFIG:
                $endpoint = Constants::ENDPOINT_CONFIG;
                break;
        }

        return $this->api->permalink . $endpoint . '/';
    }
}
