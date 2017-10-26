<?php
namespace GravApi;

use \Monolog\Logger;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GravApi\Handlers\PagesHandler;

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

    /**
     * @param $config
     */
    public function __construct($baseRoute)
    {
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

        $this->baseRoute = trim($baseRoute, '/');

        $this->attachHandlers();
    }

    protected function attachHandlers() {
        $this->app->group("/{$this->baseRoute}", function () {

            $this->group('/pages', function() {
                $this->get('', PagesHandler::class . ':getPages');
                $this->get('/{page:.*}', PagesHandler::class . ':getPage');
            });

        });
    }

    public function run()
    {
        $this->app->run();
    }
}
