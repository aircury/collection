<?php declare(strict_types=1);

namespace Aircury\Collection;

/**
 * @method void         offsetSet($offset, ?int $element)
 * @method int|null     offsetGet($offset)
 * @method int[]|null[] toArray()
 * @method int|null     first()
 * @method int|null     last()
 */
class IntegerOrNullCollection extends AbstractIntegerCollection
{
    protected function areNullsAllowed(): bool
    {
        return true;
    }
}
