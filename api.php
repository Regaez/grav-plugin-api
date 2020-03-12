<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ApiPlugin
 * @package Grav\Plugin
 */
class ApiPlugin extends Plugin
{
    protected $defaultBaseRoute = 'api';
    protected $api;

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPagesInitialized' => ['onPagesInitialized', 0]
        ]);
    }

    /**
     * @param Event $e
     */
    public function onPagesInitialized(Event $e)
    {
        $paths = $this->grav['uri']->paths();

        // Check if the requested page is an intended API call, return if not
        $apiRoute = trim($this->config->get('plugins.api.route'), '/');
        if (!$paths || $paths[0] !== $apiRoute) {
            return;
        }

        // We only run the API if enabled in the config
        if ($this->config->get('plugins.api.enabled')) {
            $this->loadApi()->run();

            // We don't need Grav to do any more
            exit();
        }
    }

    /**
     * Loads the GravApi dependencies and returns a new instance the Api app
     *
     * @return GravApi\Api $api
     */
    public function loadApi()
    {
        // Load app dependencies once we know the request is for the API
        require_once __DIR__ . '/vendor/autoload.php';
        require_once __DIR__ . '/src/Api.php';

        return new \GravApi\Api();
    }
}
