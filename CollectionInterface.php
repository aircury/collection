<?php declare(strict_types=1);

namespace Aircury\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface CollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The canonical class name that all elements on this collection are expected to be
     *
     * @return string
     */
    public function getClass(): string;

    public function offsetExists($offset): bool;

    public function offsetSet($offset, $element): void;

    public function offsetGet($offset);

    public function offsetUnset($offset): void;

    public function toArray(): array;

    public function count(): int;

    public function first();

    public function doGetFirst();

    public function getIterator(): \ArrayIterator;

    public function isAssociative(): bool;

    /**
     * Merges an array of elements to this Collection
     *
     * @param array $elements
     */
    public function merge(array $elements): void;

    /**
     * Merges a Collection of elements to this Collection
     *
     * @param AbstractCollection $collection
     */
    public function mergeCollection(AbstractCollection $collection): void;

    /**
     * Appends elements at the end of the collection, but it cannot overwrite existing elements, unless is non-associative
     *
     * @param array $elements
     */
    public function append(array $elements): void;

    /**
     * Appends a collection at the end of this collection, but it cannot overwrite existing elements, unless is non-associative
     *
     * @param AbstractCollection $collection
     */
    public function appendCollection(AbstractCollection $collection): void;

    public function prepend(array $elements): void;

    public function prependCollection(AbstractCollection $collection): void;

    public function usort(callable $sort): void;

    /**
     * @param callable $filter
     * @param bool     $returnNewCollection Return new collection or change this one
     *
     * @return $this|static
     */
    public function filter(callable $filter, bool $returnNewCollection = true);
}
