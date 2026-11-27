<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\ProtectedKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

/**
 * @phpstan-consistent-constructor
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var array<array-key, object>
     */
    private array $elements;

    private string $class;

    /**
     * @param array<mixed> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->class = $this->getClass();

        $this->assertElements($elements);

        $this->elements = $elements;
    }

    /**
     * @param array<mixed> $elements
     *
     * @phpstan-assert array<array-key, object> $elements
     */
    private function assertElements(array $elements): void
    {
        foreach ($elements as $element) {
            if (!$element instanceof $this->class) {
                throw UnexpectedElementException::classConstraint($this->class, $element);
            }
        }
    }

    /**
     * @param array<int|string> $a
     * @param array<int|string> $b
     */
    private function isSameButOutOfOrder(array $a, array $b): bool
    {
        sort($a);
        sort($b);

        return $a === $b;
    }

    /**
     * @phpstan-assert-if-true int|string $offset
     */
    public function offsetExists($offset): bool
    {
        return (\is_int($offset) || \is_string($offset)) && array_key_exists($offset, $this->elements);
    }

    public function offsetGet($offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw InvalidKeyException::invalidOffset(
                \is_scalar($offset) ? (string) $offset : get_debug_type($offset),
                array_keys($this->elements)
            );
        }

        return $this->elements[$offset];
    }

    public function offsetSet($offset, $element): void
    {
        if (!$element instanceof $this->class) {
            throw UnexpectedElementException::classConstraint($this->class, $element);
        }

        if (null === $offset) {
            $this->elements[] = $element;
        } elseif (!\is_int($offset) && !\is_string($offset)) {
            throw new \TypeError(sprintf('Illegal offset type: %s', get_debug_type($offset)));
        } else {
            $this->elements[$offset] = $element;
        }
    }

    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->elements[$offset]);
        }
    }

    /**
     * @param array<array-key, object> $elements
     */
    protected function setElements(array $elements): void
    {
        $this->elements = $elements;
    }

    /**
     * @return array<array-key, object>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return list<object>
     */
    public function toValuesArray(): array
    {
        return array_values($this->elements);
    }

    /**
     * @return int[]|string[]
     */
    public function toKeysArray(): array
    {
        return array_keys($this->elements);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @return object|false
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * @return object|false
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * @return \ArrayIterator<array-key, object>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    public function isAssociative(): bool
    {
        $keys = array_keys($this->elements);
        sort($keys);

        return $keys !== array_keys($keys);
    }

    /**
     * @inheritdoc
     *
     * @param array<mixed> $elements
     */
    public function merge(array $elements): void
    {
        $count = \count($elements);

        if (0 === $count) {
            return;
        }

        $this->assertElements($elements);

        $this->elements = array_merge($this->elements, $elements);
    }

    /**
     * @inheritdoc
     */
    public function mergeCollection(AbstractCollection $collection): void
    {
        if ($collection->isEmpty()) {
            return;
        }

        $this->elements = array_merge($this->elements, $collection->toArray());
    }

    public function mergeCollections(AbstractCollection ...$collections): void
    {
        $arguments = [];

        foreach ($collections as $collection) {
            if (!$collection->isEmpty()) {
                $arguments[] = $collection->toArray();
            }
        }

        if (empty($arguments)) {
            return;
        }

        $this->elements = array_merge($this->elements, ...$arguments);
    }

    /**
     * @inheritdoc
     *
     * @param array<mixed> $elements
     */
    public function append(array $elements): void
    {
        $count = \count($elements);

        if (0 === $count) {
            return;
        }

        if ($this->isAssociative() || 0 === \count($this->elements)) {
            if (0 !== \count(array_intersect_key($this->elements, $elements))) {
                throw ProtectedKeyException::overwritingKeys(
                    array_keys(array_intersect_key($this->elements, $elements))
                );
            }
        } else {
            $keys = array_keys($elements);
            $thisCount = \count($this->elements);

            if (
                $keys !== range(0, $count - 1) &&
                $keys !== ($range = range($thisCount, $thisCount + $count - 1)) &&
                !$this->isSameButOutOfOrder($keys, $range)
            ) {
                throw ProtectedKeyException::overwritingKeys(
                    array_keys(array_intersect_key($this->elements, $elements))
                );
            }
        }

        $this->merge($elements);
    }

    /**
     * @inheritdoc
     */
    public function appendCollection(AbstractCollection $collection): void
    {
        $this->append($collection->toArray());
    }

    /**
     * @param array<mixed> $elements
     */
    public function prepend(array $elements): void
    {
        $currentElements = $this->elements;
        $this->elements = [];

        $this->merge($elements);
        $this->append($currentElements);
    }

    public function prependCollection(AbstractCollection $collection): void
    {
        $this->prepend($collection->toArray());
    }

    public function usort(callable $sort): void
    {
        usort($this->elements, $sort);
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $filter, bool $returnNewCollection = true)
    {
        $elements = [];

        foreach ($this->toArray() as $key => $element) {
            if ($filter($element)) {
                $elements[$key] = $element;
            }
        }

        if ($returnNewCollection) {
            return new static($elements);
        }

        $this->setElements($elements);

        return $this;
    }

    /**
     * @return static
     */
    public function map(\Closure $func, bool $returnNewCollection = true)
    {
        $elements = array_map($func, $this->elements);

        if ($returnNewCollection) {
            return new static($elements);
        }

        $this->assertElements($elements);
        $this->setElements($elements);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeElement($element): bool
    {
        $key = array_search($element, $this->elements, true);

        if ($key !== false) {
            unset($this->elements[$key]);

            return true;
        }

        return false;
    }

    public function removeElementByKey(int|string $elementKey): bool
    {
        // TODO: remove column from indexes and re-define indexes
        // TODO: the removed column could be a foreign key

        if (array_key_exists($elementKey, $this->elements)) {
            unset($this->elements[$elementKey]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     *
     * @param \Traversable<mixed> $elements
     */
    public function removeElements(\Traversable $elements): bool
    {
        $return = true;

        foreach ($elements as $element) {
            $return = $this->removeElement($element) && $return;
        }

        return $return;
    }

    /**
     * @return object|null
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * @return object|null
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function debug(): array
    {

        return array_map(function ($element) {
            return $element instanceof \Stringable ? (string)$element : get_debug_type($element);
        }, $this->toArray());
    }
}
