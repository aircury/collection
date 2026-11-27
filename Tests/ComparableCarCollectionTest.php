<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\AbstractComparableCollection;
use Aircury\Collection\ComparableInterface;
use Aircury\Collection\Exceptions\UnexpectedElementException;
use Aircury\Collection\Tests\Fixtures\Car;
use Aircury\Collection\Tests\Fixtures\ComparableCar;
use Aircury\Collection\Tests\Fixtures\ComparableCarCollection;
use PHPUnit\Framework\TestCase;

class ComparableCarCollectionTest extends TestCase
{
    public function testNotComparableClass(): void
    {
        $this->expectException(UnexpectedElementException::class);
        $this->expectExceptionMessage(sprintf('but the \'%s\' doesn\'t', Car::class));

        new class extends AbstractComparableCollection {
            public function getClass(): string
            {
                return Car::class;
            }
        };
    }

    public function testContains(): void
    {
        $a = new ComparableCar('Ford');
        $cars = new ComparableCarCollection([$a]);

        $this->assertTrue($cars->contains(new ComparableCar('Ford')));
        $this->assertFalse($cars->contains(new ComparableCar('Ford'), true));
        $this->assertTrue($cars->contains($a, true));
        $this->assertFalse($cars->contains(new ComparableCar('BMW')));
    }

    public function testCompareCollections(): void
    {
        $a = new ComparableCar('Ford');
        $cars = new ComparableCarCollection([$a]);
        $sameCars = new ComparableCarCollection([$a]);
        $equalCars = new ComparableCarCollection([new ComparableCar('Ford')]);
        $otherCars = new ComparableCarCollection([new ComparableCar('BMW')]);

        $this->assertTrue($cars->compareTo(ComparableInterface::IS_SAME_AS, $equalCars));
        $this->assertFalse($cars->compareTo(ComparableInterface::IS_SAME_AS, $otherCars));
        $this->assertTrue($cars->compareTo(ComparableInterface::IS_IDENTICAL_TO, $sameCars));
        $this->assertFalse($cars->compareTo(ComparableInterface::IS_IDENTICAL_TO, $equalCars));
    }

    public function testCompareCollectionToElement(): void
    {
        $a = new ComparableCar('Ford');
        $cars = new ComparableCarCollection([$a]);

        $this->assertFalse($cars->isSameAs($a));
        $this->assertFalse($cars->isIdenticalTo($a));
    }

    public function testUnknownCollectionComparison(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ComparableCarCollection())->compareTo(99, new ComparableCarCollection());
    }

    public function testCompareElements(): void
    {
        $a = new ComparableCar('Ford');

        $this->assertTrue($a->compareTo(ComparableInterface::IS_SAME_AS, new ComparableCar('Ford')));
        $this->assertFalse($a->compareTo(ComparableInterface::IS_SAME_AS, new ComparableCar('BMW')));
        $this->assertTrue($a->compareTo(ComparableInterface::IS_IDENTICAL_TO, $a));
        $this->assertFalse($a->compareTo(ComparableInterface::IS_IDENTICAL_TO, new ComparableCar('Ford')));
    }

    public function testUnknownElementComparison(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ComparableCar('Ford'))->compareTo(99, new ComparableCar('Ford'));
    }
}
