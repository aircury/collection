<?php declare(strict_types=1);

namespace Aircury\Collection\Tests\Fixtures;

use Aircury\Collection\AbstractComparableCollection;

class ComparableCarCollection extends AbstractComparableCollection
{
    function getClass(): string
    {
        return ComparableCar::class;
    }

    public function offsetGet($offset): ComparableCar
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return ComparableCar[]
     */
    function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): ComparableCar
    {
        return $this->doGetFirst();
    }
}
