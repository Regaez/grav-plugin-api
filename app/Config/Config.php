<?php
namespace GravApi\Config;

/**
 * Class Config
 * @package GravApi\Config
 */
class Config {

    private static $instance = null;

    private $page;
    private $pages;
    private $user;
    private $users;
    private $plugin;
    private $plugins;
    private $config;
    private $configs;

    /**
     * We map all settings to existing class properties
     * @param [array] $settings
     */
    private function __construct($settings = null) {

        foreach ($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = !empty($value)
                    ? (object) $value
                    : null;
            }
        }
    }

    public static function instance($settings = null) {

        // Check if instance is already exists
        if(self::$instance == null) {
            self::$instance = new Config($settings);
        }

        return self::$instance;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}
