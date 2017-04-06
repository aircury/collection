<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\ProtectedKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $elements;

    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $isAssociative = false;

    /**
     * @var int
     */
    private $count = 0;

    public function __construct(array $elements = [])
    {
        $this->class = $this->getClass();

        foreach ($elements as $element) {
            if (!is_a($element, $this->class)) {
                throw UnexpectedElementException::classConstraint($this->class, $element);
            }
        }

        $this->elements = $elements;
        $this->count    = count($elements);

        $this->evaluateIfItIsAssociative();
    }

    private function evaluateIfItIsAssociative(): void
    {
        if (
            0 !== $this->count &&
            ($keys = array_keys($this->elements)) !== ($range = range(0, $this->count - 1)) &&
            !$this->isSameButOutOfOrder($keys, $range)
        ) {
            $this->isAssociative = true;
        }
    }

    private function isSameButOutOfOrder(array $a, array $b): bool
    {
        sort($a);
        sort($b);

        return $a === $b;
    }

    /**
     * The canonical class name that all elements on this collection are expected to be
     *
     * @return string
     */
    abstract function getClass(): string;

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->elements);
    }

    protected function doOffsetGet($offset)
    {
        if (!isset($this->elements[$offset])) {
            throw InvalidKeyException::invalidOffset($offset, array_keys($this->elements));
        }

        return $this->elements[$offset];
    }

    public function offsetSet($offset, $element): void
    {
        if (!is_a($element, $this->class)) {
            throw UnexpectedElementException::classConstraint($this->class, $element);
        }

        if (null === $offset) {
            $this->elements[] = $element;

            $this->count++;
        } else {
            $this->elements[$offset] = $element;

            if (!$this->isAssociative) {
                if (!is_int($offset) || $offset < 0 || $offset > $this->count) {
                    $this->isAssociative = true;
                } elseif ($offset === $this->count) {
                    $this->count++;
                }
            }
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);

        if (!$this->isAssociative) {
            if (is_int($offset) && $offset < $this->count - 1) {
                $this->isAssociative = true;
            } elseif ($offset === $this->count) {
                $this->count--;
            }
        }
    }

    abstract function toArray(): array;

    protected function setElements(array $elements): void
    {
        $this->elements = $elements;
    }

    protected function getElements(): array
    {
        return $this->elements;
    }

    public function count(): int
    {
        return count($this->elements);
    }

    abstract public function first();

    public function doGetFirst()
    {
        return reset($this->elements);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    public function isAssociative(): bool
    {
        return $this->isAssociative;
    }

    /**
     * Merges an array of elements to this Collection
     *
     * @param array $elements
     */
    public function merge(array $elements): void
    {
        $count = count($elements);

        if (0 === $count) {
            return;
        }

        foreach ($elements as $element) {
            if (!is_a($element, $this->class)) {
                throw UnexpectedElementException::classConstraint($this->class, $element);
            }
        }

        $this->elements = array_merge($this->elements, $elements);
        $this->count    = count($this->elements);

        $this->evaluateIfItIsAssociative();
    }

    /**
     * Merges a Collection of elements to this Collection
     *
     * @param AbstractCollection $collection
     */
    public function mergeCollection(AbstractCollection $collection): void
    {
        $count = $collection->count();

        if (0 === $count) {
            return;
        }

        $this->elements = array_merge($this->elements, $collection->getElements());
        $this->count    = count($this->elements);

        $this->evaluateIfItIsAssociative();
    }

    /**
     * Appends elements at the end of the collection, but it cannot overwrite existing elements, unless is non-associative
     *
     * @param array $elements
     */
    public function append(array $elements): void
    {
        $count = count($elements);

        if (0 === $count) {
            return;
        }

        if ($this->isAssociative || 0 === count($this->elements)) {
            if (0 !== count(array_intersect_key($this->elements, $elements))) {
                throw ProtectedKeyException::overwritingKeys(array_keys(array_intersect_key($this->elements, $elements)));
            }
        } else {
            $keys = array_keys($elements);

            if (
                $keys !== range(0, $count - 1) &&
                $keys !== ($range = range($this->count, $this->count + $count - 1)) &&
                !$this->isSameButOutOfOrder($keys, $range)
            ) {
                throw ProtectedKeyException::overwritingKeys(array_keys(array_intersect_key($this->elements, $elements)));
            }
        }

        $this->merge($elements);
    }

    /**
     * Appends a collection at the end of this collection, but it cannot overwrite existing elements, unless is non-associative
     *
     * @param AbstractCollection $collection
     */
    public function appendCollection(AbstractCollection $collection): void
    {
        $this->append($collection->toArray());
    }

    public function prepend(array $elements): void
    {
        $currentElements = $this->elements;
        $this->elements  = [];

        $this->merge($elements);
        $this->append($currentElements);
    }

    public function prependCollection(AbstractCollection $collection): void
    {
        $collection->appendCollection($this);
        $this->elements = $collection->toArray();
    }
}
