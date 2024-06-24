<?php declare(strict_types=1);

namespace Aircury\Collection;

interface CollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * The canonical class name that all elements on this collection are expected to be
     */
    public function getClass(): string;

    public function offsetExists($offset): bool;

    public function offsetSet($offset, $element): void;

    public function offsetGet($offset): mixed;

    public function offsetUnset($offset): void;

    public function toArray(): array;

    public function isEmpty(): bool;

    public function count(): int;

    public function first();

    public function last();

    public function getIterator(): \ArrayIterator;

    public function isAssociative(): bool;

    /**
     * Merges an array of elements to this Collection
     */
    public function merge(array $elements): void;

    /**
     * Merges a Collection of elements to this Collection
     */
    public function mergeCollection(AbstractCollection $collection): void;

    /**
     * Merges many Collection of elements to this Collection
     */
    public function mergeCollections(AbstractCollection ...$collections): void;

    /**
     * Appends elements at the end of the collection, but it cannot overwrite existing elements, unless is non-associative
     */
    public function append(array $elements): void;

    /**
     * Appends a collection at the end of this collection, but it cannot overwrite existing elements, unless is non-associative
     */
    public function appendCollection(AbstractCollection $collection): void;

    public function prepend(array $elements): void;

    public function prependCollection(AbstractCollection $collection): void;

    public function usort(callable $sort): void;

    /**
     * @param bool $returnNewCollection Return new collection or change this one
     *
     * @return $this|static
     */
    public function filter(callable $filter, bool $returnNewCollection = true);

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed|null $element The element to remove.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element): bool;

    /**
     * @return bool TRUE if this collection contained ALL the specified elements, FALSE otherwise.
     */
    public function removeElements(\Traversable $elements): bool;

    /**
     * @return string[] An array with the same keys, but __toString called upon each element
     */
    public function debug(): array;
}
