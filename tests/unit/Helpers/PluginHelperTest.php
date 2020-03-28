<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Helpers\PluginHelper;
use Grav\Common\Plugin;

final class PluginHelperTest extends Test
{
    /** @var Grav $client */
    protected $grav;

    protected function _before()
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();
    }

    public function testFindShouldReturnAPluginClass(): void
    {
        $this->assertInstanceOf(Plugin::class, PluginHelper::find('api'));
    }

    public function testFindShouldReturnNullWhenMissingPlugin(): void
    {
        $this->assertNull(PluginHelper::find('missing_plugin'));
    }
}
