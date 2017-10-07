<?php declare(strict_types=1);

namespace Aircury\Collection\Tests\Fixtures;

use Aircury\Collection\ComparableInterface;
use Aircury\Collection\ComparableTrait;

class ComparableCar extends Car implements ComparableInterface
{
    use ComparableTrait;
}
