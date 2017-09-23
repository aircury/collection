<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

use Aircury\Collection\AbstractCollection;

class CarCollection extends AbstractCollection
{
    public function getClass(): string
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
    public function toArray(): array
    {
        return $this->getElements();
    }

    /**
     * @return Car[]
     */
    public function toValuesArray(): array
    {
        return parent::toValuesArray();
    }

    public function first(): Car
    {
        return $this->doGetFirst();
    }
}
