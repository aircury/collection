<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

use Aircury\Collection\AbstractCollection;

class CarCollection extends AbstractCollection
{
    function getClass(): string
    {
        return Car::class;
    }

    public function offsetGet($offset): Car
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return Car[]
     */
    function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): Car
    {
        return $this->doGetFirst();
    }
}
