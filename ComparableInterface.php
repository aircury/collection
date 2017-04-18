<?php declare(strict_types=1);

namespace Aircury\Collection;

interface ComparableInterface
{
    public function isSameAs(ComparableInterface $element): bool;

    public function isIdenticalTo(ComparableInterface $element): bool;
}
