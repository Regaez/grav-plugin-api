<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use Codeception\Util\Fixtures;
use Grav\Common\Grav;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Models\ConfigModel;
use GravApi\Helpers\ConfigHelper;

final class ConfigHelperTest extends Test
{
    /** @var Grav $grav */
    protected $grav;

    protected function _before($ignore_files = array())
    {
        $grav = Fixtures::get('grav');
        $this->grav = $grav();
        Config::instance();
    }

    public function testLoadConfigsReturnsArrayOfConfigModels(): void
    {
        $configs = ConfigHelper::loadConfigs();

        $this->assertIsArray($configs);

        foreach ($configs as $config) {
            $this->assertInstanceOf(
                ConfigModel::class,
                $config
            );
        }
    }

    public function testLoadConfigsReturnsAllConfigFiles(): void
    {
        $expectedConfigs = [
            'site',
            'security',
            'streams',
            'media',
            'backups',
            'system',
            'groups'
        ];

        $configs = ConfigHelper::loadConfigs();

        foreach ($configs as $config) {
            $this->assertTrue(
                in_array($config->id, $expectedConfigs)
            );
        }
    }

    public function testLoadConfigReturnsConfigModel(): void
    {
        $config = ConfigHelper::loadConfig('site');
        $this->assertInstanceOf(ConfigModel::class, $config);
    }

    public function testLoadConfigReturnsNullForSecurityConfig(): void
    {
        $config = ConfigHelper::loadConfig('security');
        $this->assertEquals($config, null);
    }

    public function testLoadConfigReturnsNullForNoConfig(): void
    {
        $config = ConfigHelper::loadConfig('blah');
        $this->assertEquals($config, null);
    }
}
