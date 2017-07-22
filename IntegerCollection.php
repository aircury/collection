<?php declare(strict_types=1);

namespace Aircury\Collection;

class IntegerCollection extends AbstractIntegerCollection
{
    public function offsetGet($offset): int
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): int
    {
        return $this->doGetFirst();
    }
}
