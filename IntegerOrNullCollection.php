<?php declare(strict_types=1);

namespace Aircury\Collection;

class IntegerOrNullCollection extends AbstractIntegerCollection
{
    protected function areNullsAllowed(): bool
    {
        return true;
    }

    public function offsetGet($offset): ?int
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return int[]|null[]
     */
    public function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): ?int
    {
        return $this->doGetFirst();
    }
}
