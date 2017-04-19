<?php declare(strict_types=1);

namespace Aircury\Collection;

interface ComparableInterface
{
    const IS_SAME_AS = 0;
    const IS_IDENTICAL_TO = 1;

    public function isSameAs(ComparableInterface $element): bool;

    public function isIdenticalTo(ComparableInterface $element): bool;

    public function compareTo(int $comparisonMethod, ComparableInterface $element): bool;
}
