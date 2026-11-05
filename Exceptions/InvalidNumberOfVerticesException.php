<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class InvalidNumberOfVerticesException extends \LogicException
{
    private const MESSAGE = 'Invalid number of vertices given. Must be non-negative integer';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, $code, $previous);
    }
}
