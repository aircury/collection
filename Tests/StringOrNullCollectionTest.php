<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Test\Car;
use PHPUnit\Framework\TestCase;

class StringOrNullCollectionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $strings = new StringOrNullCollection();

        $strings[] = 'a';
        $strings[] = 'b';

        $this->assertEquals('a', $strings[0]);
        $this->assertEquals('b', $strings[1]);
        $this->assertCount(2, $strings);

        $this->assertTrue(isset($strings[0]));
        $this->assertTrue(isset($strings[1]));
        $this->assertFalse(isset($strings[2]));

        $this->assertEquals('a', $strings->first());
        $this->assertEquals(['a', 'b'], $strings->toArray());

        unset($strings[1]);

        $this->assertTrue(isset($strings[0]));
        $this->assertFalse(isset($strings[1]));
        $this->assertFalse(isset($strings[2]));

        $strings[] = 'x';

        $this->assertTrue(isset($strings[0]));
        $this->assertFalse(isset($strings[1]));
        $this->assertTrue(isset($strings[2]));

        $strings[20] = 'z';

        $this->assertEquals('z', $strings[20]);
        $this->assertCount(3, $strings);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementAddedToCollection(): void
    {
        $strings   = new StringOrNullCollection();
        $strings[] = 'a';
        $strings[] = 3;
    }

    public function testNullElementAddedToCollection(): void
    {
        $strings   = new StringOrNullCollection();
        $strings[] = 'a';
        $strings[] = null;

        $this->assertCount(2, $strings);

        $strings = new StringOrNullCollection([null, null, null]);

        $this->assertCount(3, $strings);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        new StringOrNullCollection(['x', 42]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        new StringOrNullCollection(['x', new Car('Volvo')]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\InvalidKeyException
     */
    public function testRetrieveByInvalidKey(): void
    {
        $strings = new StringOrNullCollection(['A' => 'a']);
        $strings['X'];
    }

    public function testIterator(): void
    {
        $a       = 'a';
        $strings = new StringOrNullCollection([$a]);

        foreach ($strings as $key => $value) {
            $this->assertEquals(0, $key);
            $this->assertEquals($a, $value);
        }
    }

    public function testImplode(): void
    {
        $strings = new StringOrNullCollection(['a', null, 'c']);

        $this->assertEquals('a, c', $strings->implode(', '));
        $this->assertEquals('a, , c', $strings->implode(', ', false));
    }
}
