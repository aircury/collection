<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Test\ComparableCar;
use Aircury\Collection\Test\ComparableCarCollection;
use PHPUnit\Framework\TestCase;

class CollectionDiffTest extends TestCase
{
    public function testAssociativeDiff(): void
    {
        $a    = new ComparableCar('Ford');
        $b    = new ComparableCar('BMW');
        $cars = new ComparableCarCollection(
            [
                'car1' => $a,
                'car2' => $b,
            ]
        );

        $noCars  = new ComparableCarCollection();
        $carDiff = new CollectionDiff($noCars, $cars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertCount(2, $carDiff->getAddedElements());
        $this->assertCount(0, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $noCarsAfterApplied = $carDiff->apply($noCars);

        $this->assertCount(2, $noCarsAfterApplied);
        $this->assertEquals($cars, $noCarsAfterApplied);

        $carDiff = new CollectionDiff($cars, $noCars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertCount(0, $carDiff->getAddedElements());
        $this->assertCount(2, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(0, $carsAfterApplied);
        $this->assertEquals($noCars, $carsAfterApplied);

        $c         = new ComparableCar('Mercedes');
        $otherCars = new ComparableCarCollection(
            [
                'car1' => $c,
                'car2' => $a,
                'car3' => $b,
            ]
        );

        $carDiff = new CollectionDiff($cars, $otherCars);

        $this->assertEquals(3, $carDiff->getChangesCount());
        $this->assertCount(1, $carDiff->getAddedElements());
        $this->assertCount(0, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(3, $carsAfterApplied);
        $this->assertEquals($otherCars, $carsAfterApplied);
    }

    public function testSequentialDiff(): void
    {
        $a    = new ComparableCar('Ford');
        $b    = new ComparableCar('BMW');
        $cars = new ComparableCarCollection([$a, $b]);

        $noCars  = new ComparableCarCollection();
        $carDiff = new CollectionDiff($noCars, $cars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertCount(2, $carDiff->getAddedElements());
        $this->assertCount(0, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $noCarsAfterApplied = $carDiff->apply($noCars);

        $this->assertCount(2, $noCarsAfterApplied);
        $this->assertEquals($cars, $noCarsAfterApplied);

        $carDiff = new CollectionDiff($cars, $noCars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertCount(0, $carDiff->getAddedElements());
        $this->assertCount(2, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(0, $carsAfterApplied);
        $this->assertEquals($noCars, $carsAfterApplied);

        $c         = new ComparableCar('Mercedes');
        $otherCars = new ComparableCarCollection([$c, $a, $b,]);

        $carDiff = new CollectionDiff($cars, $otherCars);

        $this->assertEquals(3, $carDiff->getChangesCount());
        $this->assertCount(1, $carDiff->getAddedElements());
        $this->assertCount(0, $carDiff->getRemovedElements());
        $this->assertCount(0, $carDiff->getChangedElements());

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(3, $carsAfterApplied);
        $this->assertEquals($otherCars, $carsAfterApplied);
    }
}
