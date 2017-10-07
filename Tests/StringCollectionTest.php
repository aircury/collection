<?php declare(strict_types = 1);

namespace Tests\Aircury\Collection;

use Aircury\Collection\StringCollection;
use Tests\Aircury\Collection\Test\Car;
use PHPUnit\Framework\TestCase;

class StringCollectionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $strings = new StringCollection();

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
        $strings   = new StringCollection();
        $strings[] = 'a';
        $strings[] = 3;
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testNullElementAddedToCollection(): void
    {
        $strings   = new StringCollection();
        $strings[] = 'a';
        $strings[] = null;
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        new StringCollection(['x', 42]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        new StringCollection(['x', new Car('Volvo')]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\InvalidKeyException
     */
    public function testRetrieveByInvalidKey(): void
    {
        $strings = new StringCollection(['A' => 'a']);
        $strings['X'];
    }

    public function testIterator(): void
    {
        $a       = 'a';
        $strings = new StringCollection([$a]);

        foreach ($strings as $key => $value) {
            $this->assertEquals(0, $key);
            $this->assertEquals($a, $value);
        }
    }

    public function testImplode(): void
    {
        $strings = new StringCollection(['a', 'b', 'c']);

        $this->assertEquals('a, b, c', $strings->implode(', '));
    }
}
