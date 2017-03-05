<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
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

    public function __construct(array $elements = [])
    {
        $this->class = $this->getClass();

        foreach ($elements as $element) {
            if (!is_a($element, $this->class)) {
                throw UnexpectedElementException::classConstraint($this->class, $element);
            }
        }

        $this->elements = $elements;
    }

    /**
     * The canonical class name that all elements on this collection are expected to be
     *
     * @return string
     */
    abstract function getClass(): string;

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]) || array_key_exists($offset, $this->elements);
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

        null === $offset
            ? $this->elements[] = $element
            : $this->elements[$offset] = $element;
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    abstract function toArray(): array;

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
}
