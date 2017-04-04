<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Test\Car;
use Aircury\Collection\Test\CarCollection;
use Aircury\Collection\Test\Human;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
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
        $cars[42]  = $car2;

        $this->assertCount(2, $cars);
        $this->assertEquals('Renault', $cars['x']->getMake());
        $this->assertEquals('Volvo', $cars[42]->getMake());
        $this->assertEquals(
            [
                'x' => $car1,
                42  => $car2,
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

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementAddedToCollection(): void
    {
        $cars   = new CarCollection();
        $cars[] = new Car();
        $cars[] = new Human();
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidElementPassedToCollectionConstructor(): void
    {
        new CarCollection([new Car(), new Human()]);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidTypePassedToCollectionConstructor(): void
    {
        new CarCollection([new Car(), 'x']);
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\InvalidKeyException
     */
    public function testRetrieveByInvalidKey(): void
    {
        $cars = new CarCollection(['A' => new Car('Porsche')]);
        $cars['X'];
    }

    public function testIterator(): void
    {
        $a    = new Car('Porsche');
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

        $cars  = new CarCollection([$a, $a, $b, $b]);
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
        $a    = new Car('Porsche');
        $b    = new Car('BMW');
        $cars = new CarCollection([$a]);

        $cars->merge([$b]);

        $this->assertCount(2, $cars);
        $this->assertEquals('BMW', $cars[1]->getMake());
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\UnexpectedElementException
     */
    public function testInvalidMerge(): void
    {
        $a    = new Car('Porsche');
        $b    = new Human();
        $cars = new CarCollection([$a]);

        $cars->merge([$b]);
    }

    public function testAppend(): void
    {
        $a    = new Car('Porsche');
        $b    = new Car('BMW');
        $cars = new CarCollection([$a]);

        $cars->append([$b]);

        $this->assertCount(2, $cars);
        $this->assertEquals('BMW', $cars[1]->getMake());

        $cars->append([2 => $b, 3 => $a]);

        $this->assertFalse($cars->isAssociative());
    }

    /**
     * @expectedException \Aircury\Collection\Exceptions\ProtectedKeyException
     */
    public function testInvalidAppend(): void
    {
        $a    = new Car('Porsche');
        $b    = new Car('BMW');
        $cars = new CarCollection([1 => $a]);

        $cars->append([1 => $b]);
    }

    public function testMergeCollection(): void
    {
        $a    = new Car('Porsche');
        $b    = new Car('BMW');
        $cars = new CarCollection([$a, $b]);

        $cars->mergeCollection($cars);

        $this->assertCount(4, $cars);
        $this->assertEquals('BMW', $cars[3]->getMake());
    }

    public function testAppendCollection(): void
    {
        $a     = new Car('Porsche');
        $b     = new Car('BMW');
        $cars  = new CarCollection([$a, $b]);
        $cars2 = new CarCollection([2 => $a, 3 => $b]);

        $cars->appendCollection($cars2);

        $this->assertCount(4, $cars);
        $this->assertEquals('BMW', $cars[3]->getMake());
    }

    public function testAppendCollectionToEmpty(): void
    {
        $a     = new Car('Porsche');
        $b     = new Car('BMW');
        $cars  = new CarCollection([$a, $b]);
        $empty = new CarCollection();

        $empty->appendCollection($cars);

        $this->assertCount(2, $empty);

        $cars  = new CarCollection(['a' => $a, 'b' => $b]);
        $empty = new CarCollection();

        $empty->appendCollection($cars);

        $this->assertCount(2, $empty);
    }
}
