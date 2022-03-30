<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class DuplicateVertexIdSuppliedException extends \LogicException
{
    private const MESSAGE = 'All vertex IDs supplied must be unique';

    public function __construct($code = 0, $previous = null)
    {
        parent::__construct(self::MESSAGE, $code, $previous);
    }
}
