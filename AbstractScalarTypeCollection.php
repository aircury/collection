<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

/**
 * @implements \ArrayAccess<array-key, mixed>
 * @implements \IteratorAggregate<array-key, mixed>
 */
abstract class AbstractScalarTypeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array<array-key, mixed>
     */
    private array $elements;

    private string $type;

    private bool $nullsAllowed;

    /**
     * @param array<array-key, mixed> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->type = $this->getType();
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
     */
    abstract public function getType(): string;

    protected function areNullsAllowed(): bool
    {
        return false;
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return (\is_int($offset) || \is_string($offset)) && array_key_exists($offset, $this->elements);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): mixed
    {
        // array_key_exists, not isset: isset is false for null elements, which nullable collections may hold
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
        if (gettype($element) !== $this->type && (null !== $element || !$this->nullsAllowed)) {
            throw UnexpectedElementException::typeConstraint($this->type, $element);
        }

        null === $offset
            ? ($this->elements[] = $element)
            : ($this->elements[$offset] = $element);
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function first(): mixed
    {
        return reset($this->elements);
    }

    public function last(): mixed
    {
        return end($this->elements);
    }

    /**
     * @return \ArrayIterator<array-key, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }
}
