<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GravApi\Helpers\ArrayHelper;
use Grav\Common\Grav;

final class ArrayHelperTest extends TestCase
{
    public function testCanMergeUniqueArrays(): void
    {
        $mockCurrent = [
            "one" => "foo"
        ];

        $mockNew = [
            "two" => "bar"
        ];

        $expectedResult = [
            "one" => "foo",
            "two" => "bar"
        ];
        $this->assertEquals(
            $expectedResult,
            ArrayHelper::merge($mockCurrent, $mockNew)
        );
    }

    public function testWillOverrideCurrentKeyWithNew(): void
    {
        $mockCurrent = [
            "one" => "foo"
        ];

        $mockNew = [
            "one" => "bar",
            "two" => "bar"
        ];

        $expectedResult = [
            "one" => "bar",
            "two" => "bar"
        ];

        $this->assertEquals(
            $expectedResult,
            ArrayHelper::merge($mockCurrent, $mockNew)
        );
    }
}
