<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Config\Endpoint;

final class ConfigTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();
    }

    public function testInstanceReturnsClassInstance(): void
    {
        $config = Config::instance();
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testPassingConfigReturnsNewClassInstance(): void
    {
        $firstConfig = Config::instance();
        $secondConfig = Config::instance(['route' => 'blah']);
        $this->assertNotEquals($firstConfig->route, $secondConfig->route);
        Config::resetInstance();
    }

    public function testHasDefaultRoute(): void
    {
        $config = Config::instance();
        $this->assertEquals(Constants::DEFAULT_ROUTE, $config->route);
    }

    public function testHasDefaultPermalink(): void
    {
        $config = Config::instance();
        $this->assertEquals('http://localhost/api', $config->permalink);
    }

    public function testHasDefaultEndpoints(): void
    {
        $config = Config::instance();
        $this->assertInstanceOf(Endpoint::class, $config->pages);
        $this->assertInstanceOf(Endpoint::class, $config->users);
        $this->assertInstanceOf(Endpoint::class, $config->plugins);
        $this->assertInstanceOf(Endpoint::class, $config->configs);
    }
}
