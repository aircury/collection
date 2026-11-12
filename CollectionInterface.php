<?php declare(strict_types=1);

namespace Aircury\Collection;

/**
 * @extends \ArrayAccess<array-key, object>
 * @extends \IteratorAggregate<array-key, object>
 */
interface CollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * The canonical class name that all elements on this collection are expected to be
     */
    public function getClass(): string;

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool;

    /**
     * @param mixed $offset
     * @param mixed $element Validated at runtime against getClass()
     */
    public function offsetSet($offset, $element): void;

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): mixed;

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void;

    /**
     * @return array<array-key, object>
     */
    public function toArray(): array;

    public function isEmpty(): bool;

    public function count(): int;

    /**
     * @return object|false
     */
    public function first();

    /**
     * @return object|false
     */
    public function last();

    /**
     * @return \ArrayIterator<array-key, object>
     */
    public function getIterator(): \ArrayIterator;

    public function isAssociative(): bool;

    /**
     * Merges an array of elements to this Collection
     *
     * @param array<mixed> $elements
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
     *
     * @param array<mixed> $elements
     */
    public function append(array $elements): void;

    /**
     * Appends a collection at the end of this collection, but it cannot overwrite existing elements, unless is non-associative
     */
    public function appendCollection(AbstractCollection $collection): void;

    /**
     * @param array<mixed> $elements
     */
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
     * @param \Traversable<mixed> $elements
     *
     * @return bool TRUE if this collection contained ALL the specified elements, FALSE otherwise.
     */
    public function removeElements(\Traversable $elements): bool;

    /**
     * @return string[] An array with the same keys, but __toString called upon each element
     */
    public function debug(): array;
}
