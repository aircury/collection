<?php declare(strict_types = 1);

namespace Aircury\Collection;

abstract class AbstractStringCollection extends AbstractScalarTypeCollection
{
    function getType(): string
    {
        return 'string';
    }

    public function implode($glue = '', $skipNulls = true): string
    {
        if ($this->areNullsAllowed() && $skipNulls) {
            $array = [];

            foreach ($this->toArray() as $element) {
                if (null !== $element) {
                    $array[] = $element;
                }
            }

            return implode($glue, $array);
        }

        return implode($glue, $this->toArray());
    }
}
