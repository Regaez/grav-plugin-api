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

    private function __construct($settings = null) {
        $this->page = !empty($settings['page'])
            ? (object) $settings['page']
            : null;

        $this->pages = !empty($settings['pages'])
            ? (object) $settings['pages']
            : null;

        $this->user = !empty($settings['user'])
            ? (object) $settings['user']
            : null;

        $this->users = !empty($settings['users'])
            ? (object) $settings['users']
            : null;

        $this->plugin = !empty($settings['plugin'])
            ? (object) $settings['plugin']
            : null;

        $this->plugins = !empty($settings['plugins'])
            ? (object) $settings['plugins']
            : null;
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
