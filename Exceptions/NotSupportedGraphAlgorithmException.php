<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class NotSupportedGraphAlgorithmException extends \LogicException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
