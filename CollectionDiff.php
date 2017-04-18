<?php declare(strict_types=1);

namespace Aircury\Collection;

class CollectionDiff
{
    private const ADDED = 0;
    private const REMOVED = 1;
    private const CHANGED = 2;
    private const SOURCE = 3;

    /**
     * @var ComparableInterface[][]
     */
    private $changes = [
        self::ADDED   => [],
        self::REMOVED => [],
        self::CHANGED => [],
    ];

    /**
     * @var array 'destinationIndex' => [whichSource, sourceIndex]
     */
    private $indexChanges = [];

    /**
     * @var int
     */
    private $numberChanges = 0;

    public function __construct(
        ComparableCollectionInterface $from,
        ComparableCollectionInterface $to,
        bool $strict = false
    ) {
        if (($isFromEmpty = $from->isEmpty()) && $to->isEmpty()) {
            return;
        }

        $comparisonMethod = $strict ? 'isIdenticalTo' : 'isSameAs';

        /** @var ComparableInterface[] $fromElements */
        $fromElements = $from->toArray();

        /** @var ComparableInterface[] $toElements */
        $toElements = $to->toArray();

        if ($to->isAssociative()) {
            foreach ($toElements as $key => $element) {
                if (array_key_exists($key, $fromElements)) {
                    if ($element->$comparisonMethod($fromElements[$key])) {
                        // Key exists and it is the same
                        $this->indexChanges[$key] = [self::SOURCE, $key];

                        unset($fromElements[$key]);
                    } else {
                        $this->numberChanges++;

                        if (false !== ($search = $from->search($element, $strict))) {
                            // Is there, but has another key

                            $this->indexChanges[$key] = [self::SOURCE, $search];

                            unset($fromElements[$search]);
                        } elseif (false !== $to->search($fromElements[$key], $strict)) {
                            // It is not elsewhere but the one with that index is needed later on (this element is new)

                            $this->changes[self::ADDED][$key] = $element;
                            $this->indexChanges[$key]         = [self::ADDED, $key];
                        } else {
                            // It is not elsewhere, it has changed

                            $this->changes[self::CHANGED] = $element;
                            $this->indexChanges[$key]     = [self::CHANGED, $key];

                            unset($fromElements[$key]);
                        }
                    }
                } elseif (false !== ($search = $from->search($element, $strict))) {
                    // Is there, but has another key

                    $this->indexChanges[$key] = [self::SOURCE, $search];

                    unset($fromElements[$search]);

                    $this->numberChanges++;
                } else {
                    // It is not there, it is new

                    $this->changes[self::ADDED][$key] = $element;
                    $this->indexChanges[$key]         = [self::ADDED, $key];

                    $this->numberChanges++;
                }
            }
        } else {
            $addedCount        = 0;
            $indexChangesCount = 0;

            foreach ($toElements as $element) {
                if (false !== ($search = $from->search($element, $strict))) {
                    // It is there somewhere

                    if ($search !== $indexChangesCount) {
                        $this->numberChanges++;
                    }

                    $this->indexChanges[$indexChangesCount++] = [self::SOURCE, $search];

                    unset($fromElements[$search]);
                } else {
                    // It is a new one

                    if (!$isFromEmpty) {
                        $this->indexChanges[$indexChangesCount++] = [self::ADDED, $addedCount];
                    }

                    $this->changes[self::ADDED][$addedCount++] = $element;

                    $this->numberChanges++;
                }
            }
        }

        if (!$isFromEmpty) {
            $this->changes[self::REMOVED] = $fromElements;
            $this->numberChanges          += count($fromElements);
        }
    }

    public function getChangesCount(): int
    {
        return $this->numberChanges;
    }

    public function getAddedElements(): array
    {
        return $this->changes[self::ADDED];
    }

    public function getRemovedElements(): array
    {
        return $this->changes[self::REMOVED];
    }

    public function getChangedElements(): array
    {
        return $this->changes[self::CHANGED];
    }

    public function getIndexChanges(): array
    {
        return $this->indexChanges;
    }

    public function hasReindices(): bool
    {
        return !empty($this->indexChanges) && $this->numberChanges !== count($this->changes[self::ADDED]) + count($this->changes[self::REMOVED]) + count($this->changes[self::CHANGED]);
    }

    /**
     * Applies this Diff over the collection provided and returns a new one with it applied
     *
     * @param ComparableCollectionInterface $sourceCollection
     * @param bool                          $keepOriginalOrder
     *
     * @return ComparableCollectionInterface
     */
    public function apply(ComparableCollectionInterface $sourceCollection, bool $keepOriginalOrder = false): ComparableCollectionInterface
    {
        $collectionClass = get_class($sourceCollection);

        /** @var ComparableCollectionInterface $collection */
        $collection = new $collectionClass();

        if (0 === $this->numberChanges) {
            return $collection;
        }

        if (!empty($this->indexChanges) && ($keepOriginalOrder || $this->hasReindices())) {
            foreach ($this->indexChanges as $destinationKey => [$sourceId, $sourceKey]) {
                $collection[$destinationKey] = self::SOURCE === $sourceId
                    ? $sourceCollection[$sourceKey]
                    : $this->changes[$sourceId][$sourceKey];
            }
        } else {
            $collection->merge($this->changes[self::CHANGED]);
            $collection->merge($this->changes[self::ADDED]);
        }

        return $collection;
    }
}
