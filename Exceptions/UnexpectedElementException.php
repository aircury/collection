<?php declare(strict_types = 1);

namespace Aircury\Collection\Exceptions;

class UnexpectedElementException extends \LogicException
{
    public static function typeConstraint(string $expectedType, $elementGiven): self
    {
        if (is_object($elementGiven)) {
            return new self(
                sprintf(
                    'The collection was expecting all elements to be of the type \'%s\', but an instance of the class \'%s\' was given.',
                    $expectedType,
                    get_class($elementGiven)
                )
            );
        }

        return new self(
            sprintf(
                'The collection was expecting all elements to be of the type \'%s\' class, but an element of type \'%s\' was given.',
                $expectedType,
                gettype($elementGiven)
            )
        );
    }

    public static function classConstraint(string $expectedClass, $elementGiven): self
    {
        if (is_object($elementGiven)) {
            return new self(
                sprintf(
                    'The collection was expecting all objects to be instances of the \'%s\' class, but an instance of the class \'%s\' was given.',
                    $expectedClass,
                    get_class($elementGiven)
                )
            );
        }

        return new self(
            sprintf(
                'The collection was expecting all objects to be instances of the \'%s\' class, but an element of type \'%s\' was given.',
                $expectedClass,
                gettype($elementGiven)
            )
        );
    }

    public static function notComparable(string $class): self
    {
        return new self(
            sprintf(
                'The collection was expecting the Collection to be formed by elements of a class that implements the ComparableInterface, but the \'%s\' doesn\'t.',
                $class
            )
        );
    }
}
