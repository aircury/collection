<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\CollectionDiff;
use Aircury\Collection\Tests\Fixtures\ComparableCar;
use Aircury\Collection\Tests\Fixtures\ComparableCarCollection;
use PHPUnit\Framework\TestCase;

class CollectionDiffTest extends TestCase
{
    public function testAssociativeDiff(): void
    {
        $a = new ComparableCar('Ford');
        $b = new ComparableCar('BMW');
        $cars = new ComparableCarCollection(
            [
                'car1' => $a,
                'car2' => $b,
            ]
        );

        $noCars = new ComparableCarCollection();
        $carDiff = new CollectionDiff($noCars, $cars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                'car1' => [CollectionDiff::ADDED, $a],
                'car2' => [CollectionDiff::ADDED, $b],
            ],
            $carDiff->getChanges()
        );

        $noCarsAfterApplied = $carDiff->apply($noCars);

        $this->assertCount(2, $noCarsAfterApplied);
        $this->assertEquals($cars, $noCarsAfterApplied);

        $carDiff = new CollectionDiff($cars, $noCars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                'car1' => [CollectionDiff::REMOVED, true],
                'car2' => [CollectionDiff::REMOVED, true],
            ],
            $carDiff->getChanges()
        );

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(0, $carsAfterApplied);
        $this->assertEquals($noCars, $carsAfterApplied);

        $c = new ComparableCar('Mercedes');
        $otherCars = new ComparableCarCollection(
            [
                'car1' => $c,
                'car2' => $a,
                'car3' => $b,
            ]
        );

        $carDiff = new CollectionDiff($cars, $otherCars);

        $this->assertEquals(3, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                'car1' => [CollectionDiff::ADDED, $c],
                'car2' => [CollectionDiff::SOURCE, 'car1'],
                'car3' => [CollectionDiff::SOURCE, 'car2'],
            ],
            $carDiff->getChanges()
        );

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(3, $carsAfterApplied);
        $this->assertEquals($otherCars, $carsAfterApplied);
    }

    public function testSequentialDiff(): void
    {
        $a = new ComparableCar('Ford');
        $b = new ComparableCar('BMW');
        $cars = new ComparableCarCollection([$a, $b]);

        $noCars = new ComparableCarCollection();
        $carDiff = new CollectionDiff($noCars, $cars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                [CollectionDiff::ADDED, $a],
                [CollectionDiff::ADDED, $b],
            ],
            $carDiff->getChanges()
        );

        $noCarsAfterApplied = $carDiff->apply($noCars);

        $this->assertCount(2, $noCarsAfterApplied);
        $this->assertEquals($cars, $noCarsAfterApplied);

        $carDiff = new CollectionDiff($cars, $noCars);

        $this->assertEquals(2, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                [CollectionDiff::REMOVED, true],
                [CollectionDiff::REMOVED, true],
            ],
            $carDiff->getChanges()
        );

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(0, $carsAfterApplied);
        $this->assertEquals($noCars, $carsAfterApplied);

        $c = new ComparableCar('Mercedes');
        $otherCars = new ComparableCarCollection([$c, $a, $b]);

        $carDiff = new CollectionDiff($cars, $otherCars);

        $this->assertEquals(3, $carDiff->getChangesCount());
        $this->assertEquals(
            [
                [CollectionDiff::ADDED, $c],
                [CollectionDiff::SOURCE, 0],
                [CollectionDiff::SOURCE, 1],
            ],
            $carDiff->getChanges()
        );

        $carsAfterApplied = $carDiff->apply($cars);

        $this->assertCount(3, $carsAfterApplied);
        $this->assertEqualsCanonicalizing($otherCars->toArray(), $carsAfterApplied->toArray(), '');
    }
}
