<?php declare(strict_types=1);

namespace Aircury\Collection\Tests\Fixtures;

use Aircury\Collection\AbstractComparableCollection;

/**
 * @method void                    offsetSet($offset, ComparableCar $element)
 * @method ComparableCar           offsetGet($offset)
 * @method ComparableCar[]         toArray()
 * @method ComparableCar[]         toValuesArray()
 * @method ComparableCar|null      first()
 * @method ComparableCar|null      last()
 * @method bool                    removeElement(ComparableCar $element)
 * @method ComparableCarCollection filter(callable $filter, bool $returnNewCollection = true)
 * @method ComparableCar|null      pop()
 * @method ComparableCar|null      shift()
 */
class ComparableCarCollection extends AbstractComparableCollection
{
    public function getClass(): string
    {
        return ComparableCar::class;
    }
}
