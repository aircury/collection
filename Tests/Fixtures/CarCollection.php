<?php declare(strict_types=1);

namespace Aircury\Collection\Tests\Fixtures;

use Aircury\Collection\AbstractCollection;

/**
 * @method void          offsetSet($offset, Car $element)
 * @method Car           offsetGet($offset)
 * @method Car[]         toArray()
 * @method Car[]         toValuesArray()
 * @method Car|null      first()
 * @method Car|null      last()
 * @method bool          removeElement(Car $element)
 * @method CarCollection filter(callable $filter, bool $returnNewCollection = true)
 * @method Car|null      pop()
 */
class CarCollection extends AbstractCollection
{
    public function getClass(): string
    {
        return Car::class;
    }
}
