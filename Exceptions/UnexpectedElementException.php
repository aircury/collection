<?php declare(strict_types = 1);

namespace Aircury\Collection\Exceptions;

class UnexpectedElementException extends \LogicException
{
    public static function classConstraint(string $expectedClass, $elementGiven): UnexpectedElementException
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
                'The collection was expecting all objects to be instances of the \'%s\' class, but a variable of type \'%s\' was given.',
                $expectedClass,
                gettype($elementGiven)
            )
        );
    }
}
