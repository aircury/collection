<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class VertexDoesNotExistException extends \LogicException
{
    private const MESSAGE = 'Vertex %s does not exist';

    public function __construct(string $vertexId, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $vertexId), $code, $previous);
    }
}
