<?php declare(strict_types=1);

namespace Aircury\Collection;

class StringCollection extends AbstractStringCollection
{
    public function offsetGet($offset): string
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): string
    {
        return $this->doGetFirst();
    }
}
