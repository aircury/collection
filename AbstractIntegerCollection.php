<?php declare(strict_types=1);

namespace Aircury\Collection;

abstract class AbstractIntegerCollection extends AbstractScalarTypeCollection
{
    public function getType(): string
    {
        return 'integer';
    }

    public function sum(): int
    {
        return array_sum($this->toArray());
    }
}
