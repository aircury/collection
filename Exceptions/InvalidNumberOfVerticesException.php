<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class InvalidNumberOfVerticesException extends \LogicException
{
    private const MESSAGE = 'Invalid number of vertices given. Must be non-negative integer';

    public function __construct($code = 0, $previous = null)
    {
        parent::__construct(self::MESSAGE, $code, $previous);
    }
}
