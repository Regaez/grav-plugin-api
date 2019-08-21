<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GravApi\Config\Method;

final class MethodTest extends Test
{
    public function testIsDisabledByDefault(): void
    {
        $method = new Method();
        $this->assertFalse($method->enabled);
    }

    public function testIsAuthenticatedByDefault(): void
    {
        $method = new Method();
        $this->assertTrue($method->useAuth);
    }

    public function testHasNoFieldsByDefault(): void
    {
        $method = new Method();
        $this->assertIsArray($method->fields);
        $this->assertEquals(0, count($method->fields));
    }

    public function testHasNoIgnoredFilesByDefault(): void
    {
        $method = new Method();
        $this->assertIsArray($method->ignore_files);
        $this->assertEquals(0, count($method->ignore_files));
    }

    public function testShouldBeEnabled(): void
    {
        $method = new Method([
            'enabled' => true
        ]);
        $this->assertTrue($method->enabled);
    }

    public function testEnabledIgnoresNonBoolConfig(): void
    {
        $method = new Method([
            'enabled' => 'true'
        ]);
        $this->assertFalse($method->enabled);

        $method = new Method([
            'enabled' => array(true)
        ]);
        $this->assertFalse($method->enabled);

        $method = new Method([
            'enabled' => 1
        ]);
        $this->assertFalse($method->enabled);
    }

    public function testShouldBeUnauthenticated(): void
    {
        $method = new Method([
            'auth' => false
        ]);
        $this->assertFalse($method->useAuth);
    }

    public function testUseAuthIgnoresNonBoolConfig(): void
    {
        $method = new Method([
            'auth' => 'false'
        ]);
        $this->assertTrue($method->useAuth);

        $method = new Method([
            'auth' => array(false)
        ]);
        $this->assertTrue($method->useAuth);

        $method = new Method([
            'auth' => 0
        ]);
        $this->assertTrue($method->useAuth);
    }

    public function testShouldReturnFields(): void
    {
        $method = new Method([
            'fields' => ['one', 'two', 'three']
        ]);

        $expected = ['one', 'two', 'three'];

        $this->assertEquals($expected, $method->fields);
    }

    public function testFieldsIgnoresNonStringArrayConfig(): void
    {
        $method = new Method([
            'fields' => true
        ]);
        $expected = [];

        $this->assertEquals($expected, $method->fields);

        $method = new Method([
            'fields' => [true]
        ]);
        $expected = [];

        $this->assertEquals($expected, $method->fields);

        $method = new Method([
            'fields' => [
                'one',
                1,
                'two'
            ]
        ]);
        $expected = ['one', 'two'];

        $this->assertEquals($expected, $method->fields);
    }

    public function testShouldReturnIgnoreFiles(): void
    {
        $method = new Method([
            'ignore_files' => ['one', 'two', 'three']
        ]);

        $expected = ['one', 'two', 'three'];

        $this->assertEquals($expected, $method->ignore_files);
    }

    public function testIgnoreFilesIgnoresNonStringArrayConfig(): void
    {
        $method = new Method([
            'ignore_files' => true
        ]);
        $expected = [];

        $this->assertEquals($expected, $method->ignore_files);

        $method = new Method([
            'ignore_files' => [true]
        ]);
        $expected = [];

        $this->assertEquals($expected, $method->ignore_files);

        $method = new Method([
            'ignore_files' => [
                'one',
                1,
                'two'
            ]
        ]);
        $expected = ['one', 'two'];

        $this->assertEquals($expected, $method->ignore_files);
    }
}
