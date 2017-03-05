<?php declare(strict_types = 1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\InvalidKeyException;
use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractCollection implements \ArrayAccess, \Countable
{
    /**
     * @var mixed[]
     */
    private $elements;

    /**
     * @var string
     */
    private $type;

    public function __construct(array $elements = [])
    {
        $this->type = $this->getType();

        foreach ($elements as $element) {
            if (!is_a($element, $this->type)) {
                throw UnexpectedElementException::classConstraint($this->type, $element);
            }
        }

        $this->elements = $elements;
    }

    abstract function getType(): string;

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]) || array_key_exists($offset, $this->elements);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->elements[$offset])) {
            throw InvalidKeyException::invalidOffset($offset, array_keys($this->elements));
        }

        return $this->elements[$offset];
    }

    public function offsetSet($offset, $element): void
    {
        if (!is_a($element, $this->type)) {
            throw UnexpectedElementException::classConstraint($this->type, $element);
        }

        null === $offset
            ? $this->elements[] = $element
            : $this->elements[$offset] = $element;
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function count()
    {
        return count($this->elements);
    }

    public function first()
    {
        return reset($this->elements);
    }
}
