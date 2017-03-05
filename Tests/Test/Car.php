<?php declare(strict_types = 1);

namespace Aircury\Collection\Test;

class Car
{
    /**
     * @var string
     */
    private $make;

    public function __construct(string $make = '')
    {
        $this->make = $make;
    }

    public function getMake(): string
    {
        return $this->make;
    }
}
