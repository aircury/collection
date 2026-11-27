<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class DuplicateVertexIdSuppliedException extends \LogicException
{
    private const MESSAGE = 'All vertex IDs supplied must be unique';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, $code, $previous);
    }
}
