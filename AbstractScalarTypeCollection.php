<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractScalarTypeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $elements;

    /**
     * @var string
     */
    private $type;

    /**
     * @var
     */
    private $nullsAllowed;

    public function __construct(array $elements = [])
    {
        $this->type         = $this->getType();
        $this->nullsAllowed = $this->areNullsAllowed();

        foreach ($elements as $element) {
            if (gettype($element) !== $this->type && (null !== $element || !$this->nullsAllowed)) {
                throw UnexpectedElementException::typeConstraint($this->type, $element);
            }
        }

        $this->elements = $elements;
    }

    /**
     * The PHP type all elements on this collection are expected to be ("boolean", "integer", "double", "string", "array", "object", "resource" or "NULL")
     *
     * @return string
     */
    abstract function getType(): string;

    protected function areNullsAllowed(): bool
    {
        return false;
    }

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
        if (gettype($element) !== $this->type && (null !== $element || !$this->nullsAllowed)) {
            throw UnexpectedElementException::typeConstraint($this->type, $element);
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
