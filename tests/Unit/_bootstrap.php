<?php
require '/var/www/html/vendor/autoload.php';

use Codeception\Util\Fixtures;
use Grav\Common\Grav;

ini_set('error_log', __DIR__ . '/error.log');
$grav = function () {
    Grav::resetInstance();
    $grav = Grav::instance();
    $grav['config']->init();

    foreach (array_keys($grav['setup']->getStreams()) as $stream) {
        @stream_wrapper_unregister($stream);
    }
    $grav['streams'];
    $grav['plugins']->init();
    $grav['uri']->init();
    $grav['debugger']->init();
    $grav['assets']->init();
    $grav['config']->set('system.cache.enabled', false);
    $grav['pages']->init();

    return $grav;
};

Fixtures::add('grav', $grav);
