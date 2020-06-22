<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;
use Aircury\Collection\StringCollection;
use Aircury\Collection\Tests\Fixtures\Car;
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

    public function testInvalidElementAddedToCollection(): void
    {
        $this->expectException(UnexpectedElementException::class);
        $strings = new StringCollection();
        $strings[] = 'a';
        $strings[] = 3;
    }

    public function testNullElementAddedToCollection(): void
    {
        $this->expectException(UnexpectedElementException::class);
        $strings = new StringCollection();
        $strings[] = 'a';
        $strings[] = null;
    }

    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);
        new StringCollection(['x', 42]);
    }

    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);
        new StringCollection(['x', new Car('Volvo')]);
    }

    public function testRetrieveByInvalidKey(): void
    {
        $this->expectException(InvalidKeyException::class);
        $strings = new StringCollection(['A' => 'a']);

        $strings['X'];
    }

    public function testIterator(): void
    {
        $a = 'a';
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
