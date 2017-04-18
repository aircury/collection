<?php declare(strict_types = 1);

namespace Aircury\Collection\Exceptions;

class ProtectedKeyException extends \LogicException
{
    /**
     * @param string[] $keys
     *
     * @return ProtectedKeyException
     */
    public static function overwritingKeys(array $keys): self
    {
        return new self(
            sprintf(
                'Tried to overwrite the keys \'%s\' of the collection but it is not allowed',
                implode('\', \'', $keys)
            )
        );
    }
}
