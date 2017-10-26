<?php
namespace GravApi;

use \Monolog\Logger;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
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
        $this->settings = (object) $settings;

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

        $settings = $this->settings;

        $handlers = function() use ($settings) {

            if ( !empty($settings->pages) ) {
                $this->group('/pages', function() {
                    $this->get('', PagesHandler::class . ':getPages');
                    $this->get('/{page:.*}', PagesHandler::class . ':getPage');
                });
            }

            if ( !empty($settings->users) ) {
                $this->group('/users', function() {
                    $this->get('', UsersHandler::class . ':getUsers');
                    $this->get('/{user}', UsersHandler::class . ':getUser');
                });
            }
        };

        $this->app->group("/{$this->baseRoute}", $handlers);
    }

    public function run()
    {
        $this->app->run();
    }
}
