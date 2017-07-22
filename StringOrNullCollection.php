<?php declare(strict_types=1);

namespace Aircury\Collection;

class StringOrNullCollection extends AbstractStringCollection
{
    protected function areNullsAllowed(): bool
    {
        return true;
    }

    public function offsetGet($offset): ?string
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return string[]|null[]
     */
    public function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): ?string
    {
        return $this->doGetFirst();
    }
}
