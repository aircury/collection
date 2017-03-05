<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

use Aircury\Collection\AbstractCollection;

class CarCollection extends AbstractCollection
{
    function getType(): string
    {
        return Car::class;
    }

    public function offsetGet($offset): Car
    {
        return parent::offsetGet($offset);
    }
}
