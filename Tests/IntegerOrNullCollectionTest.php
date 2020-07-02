<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;
use Aircury\Collection\IntegerOrNullCollection;
use Aircury\Collection\Tests\Fixtures\Car;
use PHPUnit\Framework\TestCase;

class IntegerOrNullCollectionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $integers = new IntegerOrNullCollection();

        $integers[] = 3;
        $integers[] = 4;

        $this->assertEquals(3, $integers[0]);
        $this->assertEquals(4, $integers[1]);
        $this->assertCount(2, $integers);

        $this->assertTrue(isset($integers[0]));
        $this->assertTrue(isset($integers[1]));
        $this->assertFalse(isset($integers[2]));

        $this->assertEquals(3, $integers->first());
        $this->assertEquals([3, 4], $integers->toArray());

        unset($integers[1]);

        $this->assertTrue(isset($integers[0]));
        $this->assertFalse(isset($integers[1]));
        $this->assertFalse(isset($integers[2]));

        $integers[] = 7;

        $this->assertTrue(isset($integers[0]));
        $this->assertFalse(isset($integers[1]));
        $this->assertTrue(isset($integers[2]));

        $integers[20] = 8;

        $this->assertEquals(8, $integers[20]);
        $this->assertCount(3, $integers);
    }

    public function testInvalidElementAddedToCollection(): void
    {
        $this->expectException(UnexpectedElementException::class);

        $integers = new IntegerOrNullCollection();
        $integers[] = 'a';
        $integers[] = 3;
    }

    public function testNullElementAddedToCollection(): void
    {
        $integers = new IntegerOrNullCollection();
        $integers[] = 3;
        $integers[] = null;

        $this->assertCount(2, $integers);

        $integers = new IntegerOrNullCollection([null, null, null]);

        $this->assertCount(3, $integers);
    }

    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);

        new IntegerOrNullCollection(['x', 42]);
    }

    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);

        new IntegerOrNullCollection([4, new Car('Volvo')]);
    }

    public function testRetrieveByInvalidKey(): void
    {
        $this->expectException(InvalidKeyException::class);

        $integers = new IntegerOrNullCollection(['A' => null]);

        $integers['X'];
    }

    public function testIterator(): void
    {
        $a = 42;
        $integers = new IntegerOrNullCollection([$a]);

        foreach ($integers as $key => $value) {
            $this->assertEquals(0, $key);
            $this->assertEquals($a, $value);
        }
    }

    public function testSum(): void
    {
        $integers = new IntegerOrNullCollection([3, null, 4]);

        $this->assertEquals(7, $integers->sum());
    }
}
