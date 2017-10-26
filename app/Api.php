<?php
namespace GravApi;

use \Monolog\Logger;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GravApi\Config\Config;
use \GravApi\Handlers\PagesHandler;
use \GravApi\Handlers\UsersHandler;

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
                    $this->get('', PagesHandler::class . ':getPages');
                }

                if ( !empty($settings->page->enabled) ) {
                    $this->get('/{page:.*}', PagesHandler::class . ':getPage');
                }
            });

            $this->group('/users', function() use ($settings) {

                if ( !empty($settings->users->enabled) ) {
                    $this->get('', UsersHandler::class . ':getUsers');
                }

                if ( !empty($settings->user->enabled) ) {
                    $this->get('/{user}', UsersHandler::class . ':getUser');
                }
            });
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
