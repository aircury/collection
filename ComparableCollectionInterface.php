<?php declare(strict_types=1);

namespace Aircury\Collection;

interface ComparableCollectionInterface extends CollectionInterface
{
    /**
     * @return bool|int|string
     */
    public function search(ComparableInterface $needle, bool $strict = false);

    public function contains(ComparableInterface $needle, bool $strict = false): bool;
}
