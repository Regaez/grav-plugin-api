<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use GravApi\Config\Constants;
use GravApi\Helpers\TaxonomyHelper;

/**
 * Class ApiPlugin
 * @package Grav\Plugin
 */
class ApiPlugin extends Plugin
{
    protected $defaultBaseRoute = 'api';
    protected $api;
    protected $admin;

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
        require_once __DIR__ . '/vendor/autoload.php';

        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onAdminRegisterPermissions' => ['onAdminRegisterPermissions', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            // We register our permissions once we can get taxonomy info
            $this->enable([
                'onPagesInitialized' => ['registerAdminPermissions', 0]
            ]);

            // Don't proceed if we are in the admin plugin
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
     * Capture admin instance so we can register roles
     * once we have the necessary taxonomy information
     *
     * @param Event $e
     */
    public function onAdminRegisterPermissions(Event $e)
    {
        if (isset($e['admin'])) {
            $this->admin = $e['admin'];
        }
    }

    /**
     * Register custom API role permissions with admin
     */
    public function registerAdminPermissions()
    {
        if ($this->admin) {
            $permissions = [];

            $roles = array_merge(
                Constants::ROLES,
                TaxonomyHelper::getRoles()
            );

            foreach ($roles as $role) {
                $permissions[$role] = 'boolean';
            }

            $this->admin->addPermissions($permissions);
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
        require_once __DIR__ . '/src/Api.php';

        return new \GravApi\Api();
    }
}
