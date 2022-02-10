<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class VertexAlreadyExistsException extends \LogicException
{
    private const MESSAGE_WITH_ID = 'Vertex %s already exists';
    private const MESSAGE_WITHOUT_ID = 'Vertex ID supplied already exists';

    public function __construct(string $vertexId = '', $code = 0, $previous = null)
    {
        $message = '' === $vertexId ? self::MESSAGE_WITHOUT_ID : sprintf(self::MESSAGE_WITH_ID, $vertexId);

        parent::__construct($message, $code, $previous);
    }
}
