<?php

namespace NsplTest\A;

use function \nspl\a\lazy\map;
use function \nspl\a\lazy\flatMap;
use function \nspl\a\lazy\zip;
use function \nspl\a\lazy\zipWith;

use const \nspl\a\lazy\map;
use const \nspl\a\lazy\flatMap;
use const \nspl\a\lazy\zip;
use const \nspl\a\lazy\zipWith;

class LazyTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $this->assertInstanceOf(\Generator::class, map('strtoupper', ['a', 'b', 'c']));

        $this->assertEquals(['A', 'B', 'C'], iterator_to_array(map('strtoupper', ['a', 'b', 'c'])));
        $this->assertEquals([1, 4, 9], iterator_to_array(map(function($v) { return $v * $v; }, new \ArrayIterator([1, 2, 3]))));
        $this->assertEquals(['a' => 0, 'b' => 1, 'c' => 2], iterator_to_array(map('abs', array('a' => 0, 'b' => -1, 'c' => 2))));
        $this->assertEquals([], iterator_to_array(map('strtoupper', [])));

        $range = function($min, $max) { for ($i = $min; $i <= $max; ++$i) yield $i; };
        $this->assertEquals([1, 4, 9], iterator_to_array(map(function($v) { return $v * $v; }, $range(1, 3))));

        $this->assertEquals(['A', 'B', 'C'], iterator_to_array(call_user_func(map, 'strtoupper', ['a', 'b', 'c'])));
        $this->assertEquals('\nspl\a\lazy\map', map);
    }

    public function testFlatMap()
    {
        $this->assertInstanceOf(\Generator::class, flatMap(function($v) { return [$v, $v + 1]; }, [1, 3]));

        $this->assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(flatMap(function($v) { return [$v, $v + 1]; }, [1, 3]))
        );

        $this->assertEquals(
            ['hello', 'world', 'answer', 'is', '42'],
            iterator_to_array(flatMap(function($v) { return explode(' ', $v); }, ['hello world', 'answer is 42']))
        );

        $this->assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(call_user_func(flatMap, function($v) { return [$v, $v + 1]; }, [1, 3]))
        );
        $this->assertEquals('\nspl\a\lazy\flatMap', flatMap);
    }

    public function testZip()
    {
        $this->assertInstanceOf(\Generator::class, zip([1, 2, 3], ['a', 'b', 'c']));

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip([1, 2, 3], ['a', 'b', 'c'])));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip(new \ArrayIterator([1, 2, 3]), ['a', 'b', 'c'])));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip([1, 2, 3], new \ArrayIterator(['a', 'b', 'c']))));
        $this->assertEquals([[1, 'a'], [2, 'b']], iterator_to_array(zip([1, 2, 3], ['a', 'b'])));
        $this->assertEquals([], iterator_to_array(zip([], ['a', 'b', 'c'])));
        $this->assertEquals([], iterator_to_array((zip([1, 2, 3], []))));

        $this->assertEquals(
            [[1, 'a', ['x']], [2, 'b', ['y']], [3, 'c', ['z']]],
            iterator_to_array(zip([1, 2, 3], ['a', 'b', 'c'], [['x'], ['y'], ['z']]))
        );

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(call_user_func(zip, [1, 2, 3], ['a', 'b', 'c'])));
        $this->assertEquals('\nspl\a\lazy\zip', zip);
    }

    public function testZipWith()
    {
        $sum = function($x, $y) { return $x + $y; };
        $sum3 = function($x, $y, $z) { return $x + $y + $z; };

        $this->assertInstanceOf(\Generator::class, zipWith($sum, [1, 2, 3], [1, 2, 3]));

        $this->assertEquals([2, 4, 6], iterator_to_array(zipWith($sum, [1, 2, 3], [1, 2, 3])));
        $this->assertEquals([3, 6, 9], iterator_to_array(zipWith($sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3])));

        $this->assertEquals([3, 6, 9], iterator_to_array(call_user_func(zipWith, $sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3])));
        $this->assertEquals('\nspl\a\lazy\zipWith', zipWith);
    }

}
