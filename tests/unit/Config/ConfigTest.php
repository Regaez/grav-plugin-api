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

    public function testInstanceReturnsSameInstance(): void
    {
        $firstConfig = Config::instance();
        $secondConfig = Config::instance();
        $this->assertEquals($firstConfig, $secondConfig);
    }

    public function testPassingConfigReturnsNewClassInstance(): void
    {
        $firstConfig = Config::instance();
        $secondConfig = Config::instance(['route' => 'blah']);
        $this->assertNotEquals($firstConfig, $secondConfig);
        Config::resetInstance();
    }

    public function testCanResetToDefaultConfigInstance(): void
    {
        $firstConfig = Config::instance();
        $secondConfig = Config::instance(['route' => 'blah']);

        $this->assertNotEquals($firstConfig, $secondConfig);

        $resetConfig = Config::resetInstance();
        $this->assertEquals($firstConfig, $resetConfig);
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

    public function testGetEndpointReturnsCorrectEndpoint(): void
    {
        $config = Config::instance();
        $this->assertEquals(
            'http://localhost/api/pages/',
            $config->getEndpoint('page')
        );
        $this->assertEquals(
            'http://localhost/api/users/',
            $config->getEndpoint('user')
        );
        $this->assertEquals(
            'http://localhost/api/plugins/',
            $config->getEndpoint('plugin')
        );
        $this->assertEquals(
            'http://localhost/api/configs/',
            $config->getEndpoint('config')
        );
    }

    public function testGetEndpointReturnsApiEndpointWhenInvalidParam(): void
    {
        $config = Config::instance();
        $this->assertEquals(
            'http://localhost/api/',
            $config->getEndpoint('blah')
        );
    }

    public function testConfigureRouteAcceptsCustomRoute(): void
    {
        $config = Config::instance(['route' => 'blah']);
        $this->assertEquals('blah', $config->route);
        Config::resetInstance();
    }

    public function testGetEndpointConfigByTypeReturnsClass(): void
    {
        $config = Config::instance();

        $this->assertEquals(
            $config->pages,
            $config->getEndpointConfigByType(Constants::TYPE_PAGE)
        );
        $this->assertEquals(
            $config->users,
            $config->getEndpointConfigByType(Constants::TYPE_USER)
        );
        $this->assertEquals(
            $config->configs,
            $config->getEndpointConfigByType(Constants::TYPE_CONFIG)
        );
        $this->assertEquals(
            $config->plugins,
            $config->getEndpointConfigByType(Constants::TYPE_PLUGIN)
        );
    }
}
