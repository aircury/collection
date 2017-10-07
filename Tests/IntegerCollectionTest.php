<?php declare(strict_types = 1);

namespace Tests\Aircury\Collection;

use Aircury\Collection\IntegerCollection;
use Tests\Aircury\Collection\Test\Car;
use PHPUnit\Framework\TestCase;

class IntegerCollectionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $integers = new IntegerCollection();

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

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementAddedToCollection(): void
    {
        $integers   = new IntegerCollection();
        $integers[] = 3;
        $integers[] = 'a';
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testNullElementAddedToCollection(): void
    {
        $integers   = new IntegerCollection();
        $integers[] = 3;
        $integers[] = null;
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        new IntegerCollection([3, 7.2]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        new IntegerCollection([42, new Car('Volvo')]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\InvalidKeyException
     */
    public function testRetrieveByInvalidKey(): void
    {
        $integers = new IntegerCollection(['A' => 3]);
        $integers['X'];
    }

    public function testIterator(): void
    {
        $a        = 3;
        $integers = new IntegerCollection([$a]);

        foreach ($integers as $key => $value) {
            $this->assertEquals(0, $key);
            $this->assertEquals($a, $value);
        }
    }

    public function testSum(): void
    {
        $integers = new IntegerCollection([3, 4, 7]);

        $this->assertEquals(14, $integers->sum());
    }
}
