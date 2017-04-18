<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\ProtectedKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractCollection implements CollectionInterface
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

    public function __construct(array $elements = [])
    {
        $this->class = $this->getClass();

        foreach ($elements as $element) {
            if (!is_a($element, $this->class)) {
                throw UnexpectedElementException::classConstraint($this->class, $element);
            }
        }

        $this->elements = $elements;

        $this->evaluateIfItIsAssociative();
    }

    private function evaluateIfItIsAssociative(): void
    {
        if (
            0 !== ($count = count($this->elements)) &&
            ($keys = array_keys($this->elements)) !== ($range = range(0, $count - 1)) &&
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
        } else {
            $this->elements[$offset] = $element;

            if (!$this->isAssociative && (!is_int($offset) || $offset < 0 || $offset > count($this->elements))) {
                $this->isAssociative = true;
            }
        }
    }

    public function offsetUnset($offset): void
    {
        if (!$this->isAssociative && (is_int($offset) && $offset < count($this->elements) - 1)) {
            $this->isAssociative = true;
        }

        unset($this->elements[$offset]);
    }

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
     * @inheritdoc
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

        $this->evaluateIfItIsAssociative();
    }

    /**
     * @inheritdoc
     */
    public function mergeCollection(AbstractCollection $collection): void
    {
        $count = $collection->count();

        if (0 === $count) {
            return;
        }

        $this->elements = array_merge($this->elements, $collection->getElements());

        $this->evaluateIfItIsAssociative();
    }

    /**
     * @inheritdoc
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
            $keys      = array_keys($elements);
            $thisCount = count($this->elements);

            if (
                $keys !== range(0, $count - 1) &&
                $keys !== ($range = range($thisCount, $thisCount + $count - 1)) &&
                !$this->isSameButOutOfOrder($keys, $range)
            ) {
                throw ProtectedKeyException::overwritingKeys(array_keys(array_intersect_key($this->elements, $elements)));
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
}
