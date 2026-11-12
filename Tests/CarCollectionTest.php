<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\ProtectedKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;
use Aircury\Collection\Tests\Fixtures\Car;
use Aircury\Collection\Tests\Fixtures\CarCollection;
use Aircury\Collection\Tests\Fixtures\Human;
use PHPUnit\Framework\TestCase;

class CarCollectionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $car1 = new Car('Renault');
        $car2 = new Car('Volvo');

        $cars = new CarCollection();

        $cars[] = $car1;
        $cars[] = $car2;

        $this->assertCount(2, $cars);
        $this->assertEquals('Renault', $cars[0]->getMake());
        $this->assertEquals('Volvo', $cars[1]->getMake());
        $this->assertEquals([$car1, $car2], $cars->toArray());
        $this->assertEquals($car1, $cars->first());
        $this->assertTrue(isset($cars[0]));
        $this->assertFalse(isset($cars[20]));

        unset($cars[0]);

        $this->assertEquals($car2, $cars->first());

        $this->assertFalse(isset($cars[0]));
        $this->assertFalse(isset($cars[20]));
    }

    public function testUsageSpecifyingOffsets(): void
    {
        $car1 = new Car('Renault');
        $car2 = new Car('Volvo');

        $cars = new CarCollection();

        $cars['x'] = $car1;
        $cars[42] = $car2;

        $this->assertCount(2, $cars);
        $this->assertEquals('Renault', $cars['x']->getMake());
        $this->assertEquals('Volvo', $cars[42]->getMake());
        $this->assertEquals(
            [
                'x' => $car1,
                42 => $car2,
            ],
            $cars->toArray()
        );
    }

    public function testPassArrayToConstructor(): void
    {
        $carArray = [
            new Car('Renault'),
            new Car('Volvo'),
        ];

        $cars = new CarCollection($carArray);

        $this->assertCount(2, $cars);
    }

    public function testInvalidElementAddedToCollection(): void
    {
        $this->expectException(UnexpectedElementException::class);

        $cars = new CarCollection();
        $cars[] = new Car();
        // @phpstan-ignore offsetAssign.valueType (the test checks that an invalid element is rejected)
        $cars[] = new Human();
    }

    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);

        new CarCollection([new Car(), new Human()]);
    }

    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        $this->expectException(UnexpectedElementException::class);

        new CarCollection([new Car(), 'x']);
    }

    public function testRetrieveByInvalidKey(): void
    {
        $this->expectException(InvalidKeyException::class);

        $cars = new CarCollection(['A' => new Car('Porsche')]);

        // @phpstan-ignore expr.resultUnused (the test checks that reading a missing key throws)
        $cars['X'];
    }

    public function testIterator(): void
    {
        $a = new Car('Porsche');
        $cars = new CarCollection([$a]);

        foreach ($cars as $key => $value) {
            $this->assertEquals(0, $key);
            $this->assertEquals($a, $value);
        }
    }

    public function testAssociative(): void
    {
        $cars = new CarCollection();

        $this->assertFalse($cars->isAssociative());

        $a = new Car('Porsche');
        $b = new Car('BMW');

        $cars[] = $a;
        $cars[] = $b;

        $this->assertFalse($cars->isAssociative());

        $cars[2] = $b;

        $this->assertFalse($cars->isAssociative());

        unset($cars[2]);

        $this->assertFalse($cars->isAssociative());

        unset($cars[0]);

        $this->assertTrue($cars->isAssociative());

        $cars = new CarCollection([$a, $a, $b, $b]);
        $cars2 = clone $cars;

        $this->assertFalse($cars->isAssociative());

        unset($cars[1]);

        $this->assertTrue($cars->isAssociative());

        unset($cars2[0]);

        $this->assertTrue($cars->isAssociative());

        $cars = new CarCollection(['0' => $a]);

        $this->assertFalse($cars->isAssociative());

        $cars['1'] = $b;

        $this->assertFalse($cars->isAssociative());

        $cars = new CarCollection([1 => $a, 2 => $a, 0 => $b]);

        $this->assertFalse($cars->isAssociative());
    }

    public function testMerge(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([$a]);

        $cars->merge([$b]);

        $this->assertCount(2, $cars);
        $this->assertEquals('BMW', $cars[1]->getMake());
    }

    public function testInvalidMerge(): void
    {
        $this->expectException(UnexpectedElementException::class);

        $a = new Car('Porsche');
        $b = new Human();
        $cars = new CarCollection([$a]);

        $cars->merge([$b]);
    }

    public function testAppend(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([$a]);

        $cars->append([$b]);

        $this->assertCount(2, $cars);
        $this->assertEquals('BMW', $cars[1]->getMake());

        $cars->append([2 => $b, 3 => $a]);

        $this->assertFalse($cars->isAssociative());
    }

    public function testInvalidAppend(): void
    {
        $this->expectException(ProtectedKeyException::class);

        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([1 => $a]);

        $cars->append([1 => $b]);
    }

    public function testMergeCollection(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([$a, $b]);

        $cars->mergeCollection($cars);

        $this->assertCount(4, $cars);
        $this->assertEquals('BMW', $cars[3]->getMake());
    }

    public function testAppendCollection(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([$a, $b]);
        $cars2 = new CarCollection([2 => $a, 3 => $b]);

        $cars->appendCollection($cars2);

        $this->assertCount(4, $cars);
        $this->assertEquals('BMW', $cars[3]->getMake());
    }

    public function testAppendCollectionToEmpty(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection([$a, $b]);
        $empty = new CarCollection();

        $empty->appendCollection($cars);

        $this->assertCount(2, $empty);

        $cars = new CarCollection(['a' => $a, 'b' => $b]);
        $empty = new CarCollection();

        $empty->appendCollection($cars);

        $this->assertCount(2, $empty);
    }

    public function testRetrieveByInvalidIntegerKey(): void
    {
        $this->expectException(InvalidKeyException::class);

        $cars = new CarCollection([new Car('Porsche')]);

        $cars->offsetGet(5);
    }

    public function testRemoveElements(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection([$a, $b]);

        $this->assertTrue($cars->removeElements(new \ArrayIterator([$a, $b])));
        $this->assertCount(0, $cars);

        $cars = new CarCollection([$a]);

        $this->assertFalse($cars->removeElements(new \ArrayIterator([$a, $c])));
        $this->assertCount(0, $cars);
    }

    public function testInvalidElementMappedInPlace(): void
    {
        $this->expectException(UnexpectedElementException::class);

        $cars = new CarCollection([new Car('Porsche')]);

        $cars->map(static fn(Car $car): string => $car->getMake(), false);
    }

    public function testAssociativeAfterChanges(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');

        $cars = new CarCollection([$a, $b]);
        $cars[3] = $b;

        $this->assertTrue($cars->isAssociative());

        $cars = new CarCollection([$a, $b, $a]);
        $cars->removeElement($b);

        $this->assertTrue($cars->isAssociative());

        $cars = new CarCollection(['x' => $a, 'y' => $b]);
        $cars->usort(static fn(Car $left, Car $right): int => $left->getMake() <=> $right->getMake());

        $this->assertFalse($cars->isAssociative());
    }

    public function testAppendOutOfOrderKeys(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $d = new Car('Seat');
        $cars = new CarCollection([$a, $b]);

        $cars->append([3 => $d, 2 => $c]);

        $this->assertSame([$a, $b, $d, $c], $cars->toArray());
    }

    public function testValuesAndKeysArrays(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection(['x' => $a, 42 => $b]);

        $this->assertSame([$a, $b], $cars->toValuesArray());
        $this->assertSame(['x', 42], $cars->toKeysArray());
    }

    public function testLast(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');

        $this->assertSame($b, (new CarCollection([$a, $b]))->last());
        $this->assertFalse((new CarCollection())->last());
    }

    public function testPopAndShift(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection([$a, $b, $c]);

        $this->assertSame($c, $cars->pop());
        $this->assertSame($a, $cars->shift());
        $this->assertSame([$b], $cars->toArray());

        $empty = new CarCollection();

        $this->assertNull($empty->pop());
        $this->assertNull($empty->shift());
    }

    public function testMergeCollections(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection([$a]);

        $cars->mergeCollections(new CarCollection([$b]), new CarCollection(), new CarCollection([$c]));

        $this->assertSame([$a, $b, $c], $cars->toArray());

        $cars->mergeCollections(new CarCollection(), new CarCollection());

        $this->assertSame([$a, $b, $c], $cars->toArray());
    }

    public function testPrepend(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection([$a, $b]);

        $cars->prepend([$c]);

        $this->assertSame([$c, $a, $b], $cars->toArray());
    }

    public function testPrependCollection(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection([$a, $b]);

        $other = new CarCollection([$c]);

        $cars->prependCollection($other);

        $this->assertSame([$c, $a, $b], $cars->toArray());
        $this->assertSame([$c], $other->toArray());
    }

    public function testFilter(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $c = new Car('Audi');
        $cars = new CarCollection(['a' => $a, 'b' => $b, 'c' => $c]);
        $notBmw = static fn(Car $car): bool => 'BMW' !== $car->getMake();

        $filtered = $cars->filter($notBmw);

        $this->assertInstanceOf(CarCollection::class, $filtered);
        $this->assertNotSame($cars, $filtered);
        $this->assertSame(['a' => $a, 'c' => $c], $filtered->toArray());
        $this->assertCount(3, $cars);

        $this->assertSame($cars, $cars->filter($notBmw, false));
        $this->assertSame(['a' => $a, 'c' => $c], $cars->toArray());
    }

    public function testRemoveElementByKey(): void
    {
        $a = new Car('Porsche');
        $b = new Car('BMW');
        $cars = new CarCollection(['a' => $a, 'b' => $b]);

        $this->assertTrue($cars->removeElementByKey('a'));
        $this->assertFalse($cars->removeElementByKey('a'));
        $this->assertSame(['b' => $b], $cars->toArray());
    }

    public function testDebug(): void
    {
        $named = new class('Porsche') extends Car implements \Stringable {
            public function __toString(): string
            {
                return $this->getMake();
            }
        };
        $cars = new CarCollection(['named' => $named, 'plain' => new Car('BMW')]);

        $this->assertSame(['named' => 'Porsche', 'plain' => Car::class], $cars->debug());
    }

    public function testIllegalOffsetType(): void
    {
        $this->expectException(\TypeError::class);

        $cars = new CarCollection();

        $cars->offsetSet(1.5, new Car('Porsche'));
    }

    public function testAddingNothing(): void
    {
        $a = new Car('Porsche');
        $cars = new CarCollection([$a]);

        $cars->merge([]);
        $cars->mergeCollection(new CarCollection());
        $cars->append([]);

        $this->assertSame([$a], $cars->toArray());
    }

    public function testInvalidAppendToList(): void
    {
        $this->expectException(ProtectedKeyException::class);

        $cars = new CarCollection([new Car('Porsche'), new Car('BMW')]);

        $cars->append([5 => new Car('Audi')]);
    }

    public function testMap(): void
    {
        $cars = new CarCollection(['a' => new Car('Porsche')]);
        $upper = static fn(Car $car): Car => new Car(strtoupper($car->getMake()));

        $mapped = $cars->map($upper);

        $this->assertNotSame($cars, $mapped);
        $this->assertSame('PORSCHE', $mapped['a']->getMake());
        $this->assertSame('Porsche', $cars['a']->getMake());

        $this->assertSame($cars, $cars->map($upper, false));
        $this->assertSame('PORSCHE', $cars['a']->getMake());
    }
}
