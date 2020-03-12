<?php
namespace GravApi\Config;

use Grav\Common\Grav;
use GravApi\Config\Constants;
use GravApi\Config\Endpoint;

/**
 * Class Config
 * @package GravApi\Config
 */
class Config
{
    /**
     * @var Config|null
     */
    private static $instance = null;

    /**
     * @var string
     */
    protected $route = Constants::DEFAULT_ROUTE;

    /**
     * @var string
     */
    protected $permalink;

    /**
     * @var Endpoint
     */
    protected $pages;

    /**
     * @var Endpoint
     */
    protected $users;

    /**
     * @var Endpoint
     */
    protected $plugins;

    /**
     * @var Endpoint
     */
    protected $configs;

    /**
     * We map all settings to existing class properties
     * @param [array] $settings
     */
    private function __construct($config = null)
    {
        $route = isset($config['route'])
            ? $config['route']
            : null;

        $this->configureRoute($route);

        $endpoints = isset($config['endpoints'])
            ? $config['endpoints']
            : null;

        $this->configureEndpoints($endpoints);
    }

    public static function instance($settings = null)
    {
        if (!isset($settings) || !is_array($settings)) {
            $settings = array();
        }

        // Check if instance is already exists, or
        // Recreate config instance if new settings are passed
        if (self::$instance == null || !empty($settings)) {
            self::$instance = new Config($settings);
        }

        return self::$instance;
    }

    public static function resetInstance()
    {
        self::$instance = new Config();

        return self::$instance;
    }

    public function __get(string $property)
    {
        // We only allow access to specific properties
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return null;
    }

    /**
     * Initialise all the endpoint settings
     */
    protected function configureEndpoints($config = null)
    {
        if (!isset($config) || !is_array($config)) {
            $config = Grav::instance()['config']->get('plugins.api.endpoints');
        }

        // check once more that our plugin config is also an array
        if (!is_array($config)) {
            $config = array();
        }

        foreach (Constants::ENDPOINTS as $endpoint) {
            $params = isset($config[$endpoint])
                ? $config[$endpoint]
                : null;

            // Remove leading slash to match our Config property
            $configProperty = ltrim($endpoint, '/');

            $this->{$configProperty} = new Endpoint($params);
        }
    }

    protected function configureRoute($customRoute = null)
    {
        // We default to using our Constant
        // Then we try to pull the route from the Api plugin config
        $route = Grav::instance()['config']->get('plugins.api.route');

        // But may want to override it specifically if we've given custom
        // settings to the Config instance (e.g. for tests)
        if (isset($customRoute)) {
            $this->route = $customRoute;
        } elseif ($route) {
            $this->route = trim($route, '/');
        }

        // Finally, update the permalink to use new route
        $this->setPermalink();
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

        return $this->permalink . $endpoint . '/';
    }

    protected function setPermalink()
    {
        $rootUrl = Grav::instance()['uri']->rootUrl(true);
        $this->permalink = $rootUrl . '/' . $this->route;
    }
}
