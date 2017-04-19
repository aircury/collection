<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractComparableCollection extends AbstractCollection implements ComparableCollectionInterface, ComparableInterface
{
    public function __construct(array $elements = [])
    {
        if (!is_a($this->getClass(), ComparableInterface::class, true)) {
            throw UnexpectedElementException::notComparable($this->getClass());
        }

        parent::__construct($elements);
    }

    /**
     * @param ComparableInterface $needle
     * @param bool                $strict
     *
     * @return bool|int|string
     */
    public function search(ComparableInterface $needle, bool $strict = false)
    {
        $comparisonMethod = $strict ? ComparableInterface::IS_IDENTICAL_TO : ComparableInterface::IS_SAME_AS;

        foreach ($this->toArray() as $key => $element) {
            if ($needle->compareTo($comparisonMethod, $element)) {
                return $key;
            }
        }

        return false;
    }

    public function contains(ComparableInterface $needle, bool $strict = false): bool
    {
        return false !== $this->search($needle, $strict);
    }

    public function compareTo(int $comparisonMethod, ComparableInterface $element): bool
    {
        switch ($comparisonMethod) {
            case ComparableInterface::IS_SAME_AS:
                return $this->isSameAs($element);
            case ComparableInterface::IS_IDENTICAL_TO:
                return $this->isIdenticalTo($element);
            default:
                throw new \InvalidArgumentException('Unknown comparison method provided.');
        }
    }

    public function isSameAs(ComparableInterface $collection): bool
    {
        if (!$collection instanceof ComparableCollectionInterface) {
            return false;
        }

        return $this->toArray() == $collection->toArray();
    }

    public function isIdenticalTo(ComparableInterface $collection): bool
    {
        if (!$collection instanceof ComparableCollectionInterface) {
            return false;
        }

        return $this->toArray() === $collection->toArray();
    }
}
