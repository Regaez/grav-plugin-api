<?php
namespace GravApi;

use Grav\Common\Grav;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class Api
 * @package GravApi
 */
class Api
{

    // Our Slim app instance
    protected $app;

    // Our grav instance
    protected $grav;

    // Our base API route
    protected $baseRoute;

    /**
     * @param $config
     */
    public function __construct($baseRoute)
    {
        // Initialise Slim
        $container = new \Slim\Container;
        $this->app = new \Slim\App($container);

        $this->grav = Grav::instance();

        $this->baseRoute = trim($baseRoute, '/');

        $this->app->get("/{$this->baseRoute}".'/hello/{name}', function (Request $request, Response $response) {
            $name = $request->getAttribute('name');
            $response->getBody()->write("Hello, $name");
            return $response;
        });

        $this->app->run();
    }
}
