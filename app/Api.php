<?php
namespace GravApi;

use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Grav\Common\Grav;
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

    // Our Grav plugin endpoint settings
    protected $settings;

    /**
     * @param $config
     */
    public function __construct($settings)
    {
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
        $this->app->group("/{$this->settings->api->route}", function() {

            $this->get('', function($request, $response, $args) {
                $settings = Config::instance();
                $url = $settings->api->permalink;

                $urls = [
                    'pages' => "{$url}/pages",
                    'users' => "{$url}/users",
                    'config' => "{$url}/config",
                    'plugins' => "{$url}/plugins",
                ];

                return $response->withJson($urls);
            });

            $settings = Config::instance();

            $this->group('/pages', function() use ($settings) {

                if ( !empty($settings->pages->get['enabled']) ) {
                    $this->get('', PagesHandler::class . ':getPages')
                         ->add(new AuthMiddleWare($settings->pages));

                    $this->get('/{page:.*}', PagesHandler::class . ':getPage')
                         ->add(new AuthMiddleWare($settings->pages->get));
                }

                if ( !empty($settings->pages->post['enabled']) ) {
                    $this->post('', PagesHandler::class . ':newPage')
                         ->add(new AuthMiddleWare($settings->pages->post));
                }

                if ( !empty($settings->pages->delete['enabled']) ) {
                    $this->delete('/{page:.*}', PagesHandler::class . ':deletePage')
                         ->add(new AuthMiddleWare($settings->pages->delete));
                }

                if ( !empty($settings->pages->patch['enabled']) ) {
                    $this->patch('/{page:.*}', PagesHandler::class . ':updatePage')
                         ->add(new AuthMiddleWare($settings->pages->patch));
                }
            });

            $this->group('/users', function() use ($settings) {

                if ( !empty($settings->users->get['enabled']) ) {
                    $this->get('', UsersHandler::class . ':getUsers')
                         ->add(new AuthMiddleWare($settings->users->get));

                    $this->get('/{user}', UsersHandler::class . ':getUser')
                         ->add(new AuthMiddleWare($settings->users->get));
                }

                if ( !empty($settings->users->post['enabled']) ) {
                    $this->post('', UsersHandler::class . ':newUser')
                         ->add(new AuthMiddleWare($settings->users->post));
                }

                if ( !empty($settings->users->delete['enabled']) ) {
                    $this->delete('/{user}', UsersHandler::class . ':deleteUser')
                         ->add(new AuthMiddleWare($settings->users->delete));
                }

                if ( !empty($settings->users->patch['enabled']) ) {
                    $this->patch('/{user}', UsersHandler::class . ':updateUser')
                         ->add(new AuthMiddleWare($settings->users->patch));
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
