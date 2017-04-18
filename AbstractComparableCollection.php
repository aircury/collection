<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\UnexpectedElementException;

abstract class AbstractComparableCollection extends AbstractCollection implements ComparableCollectionInterface
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
        $comparisonMethod = $strict ? 'isIdenticalTo' : 'isSameAs';

        foreach ($this->toArray() as $key => $element) {
            if ($needle->$comparisonMethod($element)) {
                return $key;
            }
        }

        return false;
    }

    public function contains(ComparableInterface $needle, bool $strict = false): bool
    {
        return false !== $this->search($needle, $strict);
    }
}
