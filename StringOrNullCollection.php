<?php declare(strict_types=1);

namespace Aircury\Collection;

/**
 * @method void            offsetSet($offset, ?string $element)
 * @method string|null     offsetGet($offset)
 * @method null[]|string[] toArray()
 * @method string|null     first()
 * @method string|null     last()
 */
class StringOrNullCollection extends AbstractStringCollection
{
    protected function areNullsAllowed(): bool
    {
        return true;
    }
}
