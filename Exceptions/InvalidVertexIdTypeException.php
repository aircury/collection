<?php declare(strict_types=1);

namespace Aircury\Collection\Exceptions;

class InvalidVertexIdTypeException extends \LogicException
{
    private const MESSAGE_WITH_ID = 'Vertex ID type not allowed for \'%s\'. Allowed types: string, integer';
    private const MESSAGE_WITHOUT_ID = 'Vertex ID type not allowed. Allowed types: string, integer';

    public function __construct(string $vertexId = '', $code = 0, $previous = null)
    {
        $message = '' === $vertexId ? self::MESSAGE_WITHOUT_ID : sprintf(self::MESSAGE_WITH_ID, $vertexId);

        parent::__construct($message, $code, $previous);
    }
}
