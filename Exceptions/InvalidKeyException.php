<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class InvalidKeyException extends \LogicException
{
    /**
     * @param string   $offset
     * @param string[] $validOffsets
     *
     * @return InvalidKeyException
     */
    public static function invalidOffset(string $offset, array $validOffsets = []): self
    {
        return new self(
            sprintf(
                'The key \'%s\' does not exist on the collection.%s',
                $offset,
                0 !== \count($validOffsets)
                    ? sprintf(' The valid keys are: \'%s\'', implode('\', \'', $validOffsets))
                    : ''
            )
        );
    }
}
