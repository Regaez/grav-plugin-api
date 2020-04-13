<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GravApi\Helpers\TaxonomyHelper;

final class TaxonomyHelperTest extends Test
{
    public function testMergeReturnsDistinctValues(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'two' => [
                'bar'
            ]
        ];

        $expectedResult = [
            'one' => ['foo'],
            'two' => ['bar']
        ];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::merge($a, $b)
        );
    }

    public function testMergeRemovesDuplicateValues(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'foo'
            ],
            'two' => [
                'bar'
            ]
        ];

        $expectedResult = [
            'one' => ['foo'],
            'two' => ['bar']
        ];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::merge($a, $b)
        );
    }

    public function testMergeAppendsNewValues(): void
    {
        $a = [
            'one' => [
                'boo'
            ]
        ];

        $b = [
            'one' => [
                'foo'
            ]
        ];

        $expectedResult = [
            'one' => ['boo', 'foo'],
        ];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::merge($a, $b)
        );
    }

    public function testIntersectRemovesUniqueKeys(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'foo'
            ],
            'two' => [
                'bar'
            ]
        ];

        $expectedResult = [
            'one' => ['foo']
        ];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::intersect($a, $b)
        );
    }

    public function testIntersectRemovesRemovesUniqueValues(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'foo',
                'bar'
            ]
        ];

        $expectedResult = [
            'one' => ['foo']
        ];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::intersect($a, $b)
        );
    }

    public function testIntersectReturnsEmptyArray(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'bar'
            ]
        ];

        $expectedResult = [];
        $this->assertEquals(
            $expectedResult,
            TaxonomyHelper::intersect($a, $b)
        );
    }

    public function testHasIntersectReturnsTrue(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'foo'
            ]
        ];

        $this->assertTrue(TaxonomyHelper::hasIntersect($a, $b));
    }

    public function testHasIntersectReturnsFalse(): void
    {
        $a = [
            'one' => [
                'foo'
            ]
        ];

        $b = [
            'one' => [
                'bar'
            ]
        ];

        $this->assertFalse(TaxonomyHelper::hasIntersect($a, $b));
    }
}
