<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class NotADirectedAcyclicGraphException extends \UnexpectedValueException
{
    private const MESSAGE = 'The graph is not a Directed Acyclic Graph (DAG). Cycles and/or loops detected.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
