<?php
namespace GravApi\Handlers;

use Grav\Common\Grav;
use GravApi\Config\Config;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class BaseHandler
{
    // Our grav instance
    protected $grav;

    // Our endpoint config
    protected $config;

    // constructor receives container instance
    public function __construct()
    {
        $this->grav = Grav::instance();
        $this->config = Config::instance();
    }
}
