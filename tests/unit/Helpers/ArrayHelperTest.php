<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GravApi\Helpers\ArrayHelper;

final class ArrayHelperTest extends Test
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

    public function testAsStringArrayWillIgnoreNonStringChildren(): void
    {
        $mockArray = [
            "foo",
            array('ignored'),
            true,
            0
        ];

        $expectedResult = array('foo');

        $this->assertEquals(
            $expectedResult,
            ArrayHelper::asStringArray($mockArray)
        );
    }
}
