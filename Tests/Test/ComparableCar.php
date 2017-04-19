<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

use Aircury\Collection\ComparableInterface;

class ComparableCar extends Car implements ComparableInterface
{
    public function compareTo(int $comparisonMethod, ComparableInterface $element): bool
    {
        switch ($comparisonMethod) {
            case ComparableInterface::IS_SAME_AS:
                return $this->isSameAs($element);
            case ComparableInterface::IS_IDENTICAL_TO:
                return $this->isIdenticalTo($element);
            default:
                throw new \InvalidArgumentException('Unknown comparison method provided.');
        }
    }

    public function isSameAs(ComparableInterface $element): bool
    {
        return $this == $element;
    }

    public function isIdenticalTo(ComparableInterface $element): bool
    {
        return $this === $element;
    }
}
