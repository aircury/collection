<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

use Aircury\Collection\ComparableInterface;

class ComparableCar extends Car implements ComparableInterface
{
    public function isSameAs(ComparableInterface $element): bool
    {
        return $this == $element;
    }

    public function isIdenticalTo(ComparableInterface $element): bool
    {
        return $this === $element;
    }
}
