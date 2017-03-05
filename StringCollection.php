<?php declare(strict_types = 1);

namespace Aircury\Collection;

class StringCollection extends AbstractScalarTypeCollection
{
    function getType(): string
    {
        return 'string';
    }

    public function offsetGet($offset): string
    {
        return $this->doOffsetGet($offset);
    }

    /**
     * @return string[]
     */
    function toArray(): array
    {
        return $this->getElements();
    }

    public function first(): string
    {
        return $this->doGetFirst();
    }
}
