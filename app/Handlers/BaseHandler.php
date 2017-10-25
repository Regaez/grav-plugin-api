<?php
namespace GravApi\Handlers;

use Grav\Common\Grav;
use Interop\Container\ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class BaseHandler
{
    // Our Slim container
    protected $container;

    // Our grav instance
    protected $grav;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->grav = Grav::instance();
    }
}
