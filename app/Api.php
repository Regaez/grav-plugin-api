<?php
namespace GravApi;

use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GravApi\Config\Config;
use GravApi\Middlewares\AuthMiddleWare;
use GravApi\Handlers\ConfigHandler;
use GravApi\Handlers\PagesHandler;
use GravApi\Handlers\PluginsHandler;
use GravApi\Handlers\UsersHandler;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class Api
 * @package GravApi
 */
class Api
{
    // Our Slim app instance
    protected $app;

    // Our base API route
    protected $baseRoute;

    // Our Grav plugin endpoint settings
    protected $settings;

    /**
     * @param $config
     */
    public function __construct($baseRoute, $settings)
    {
        $this->baseRoute = trim($baseRoute, '/');
        $this->settings = Config::instance($settings);

        // Initialise Slim
        $config = [
            'settings' => [
                'displayErrorDetails' => true,
                'logger' => [
                    'name' => 'slim-app',
                    'level' => Logger::DEBUG,
                    'path' => __DIR__ . '/../logs/app.log',
                ],
            ]
        ];
        $this->app = new \Slim\App($config);

        $this->attachHandlers();
    }

    protected function attachHandlers() {

        // We must serve from the base route
        $this->app->group("/{$this->baseRoute}", function() {

            $settings = Config::instance();

            $this->group('/pages', function() use ($settings) {

                if ( !empty($settings->pages->enabled) ) {
                    $this->get('', PagesHandler::class . ':getPages')
                         ->add(new AuthMiddleWare($settings->pages));
                }

                if ( !empty($settings->page->enabled) ) {
                    $this->get('/{page:.*}', PagesHandler::class . ':getPage')
                         ->add(new AuthMiddleWare($settings->page));
                }
            });

            $this->group('/users', function() use ($settings) {

                if ( !empty($settings->users->enabled) ) {
                    $this->get('', UsersHandler::class . ':getUsers')
                         ->add(new AuthMiddleWare($settings->users));
                }

                if ( !empty($settings->user->enabled) ) {
                    $this->get('/{user}', UsersHandler::class . ':getUser')
                         ->add(new AuthMiddleWare($settings->user));
                }
            });

            $this->group('/plugins', function() use ($settings) {

                if ( !empty($settings->plugins->enabled) ) {
                    $this->get('', PluginsHandler::class . ':getPlugins')
                         ->add(new AuthMiddleWare($settings->plugins));
                }

                if ( !empty($settings->plugin->enabled) ) {
                    $this->get('/{plugin}', PluginsHandler::class . ':getPlugin')
                         ->add(new AuthMiddleWare($settings->plugin));
                }
            });

            $this->group('/config', function() use ($settings) {

                if ( !empty($settings->configs->enabled) ) {
                    $this->get('', ConfigHandler::class . ':getConfigs')
                         ->add(new AuthMiddleWare($settings->configs));
                }

                if ( !empty($settings->config->enabled) ) {
                    $this->get('/{config}', ConfigHandler::class . ':getConfig')
                         ->add(new AuthMiddleWare($settings->config));
                }
            });
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
