<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Test\Car;
use Aircury\Collection\Test\CarCollection;
use Aircury\Collection\Test\Human;
use PHPUnit_Framework_TestCase;

class ModelTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals([$car1, $car2], $cars->getElements());
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
            $cars->getElements()
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
}
