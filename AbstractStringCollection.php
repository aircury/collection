<?php declare(strict_types=1);

namespace Aircury\Collection;

abstract class AbstractStringCollection extends AbstractScalarTypeCollection
{
    public function getType(): string
    {
        return 'string';
    }

    public function implode(string $glue = '', bool $skipNulls = true): string
    {
        $strings = [];

        foreach ($this->toArray() as $element) {
            if (\is_string($element)) {
                $strings[] = $element;
            } elseif (!$skipNulls) {
                $strings[] = '';
            }
        }

        return implode($glue, $strings);
    }
}
