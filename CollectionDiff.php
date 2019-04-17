<?php declare(strict_types=1);

namespace Aircury\Collection;

class CollectionDiff
{
    public const SOURCE = 0;
    public const ADDED = 1;
    public const REMOVED = 2;
    public const CHANGED = 3;

    /**
     * @var ComparableInterface[]|int[] 'destinationIndex' => [whichSource, sourceIndex]
     */
    private $changes = [];

    /**
     * @var int
     */
    private $numberChanges = 0;

    /**
     * @param ComparableCollectionInterface $from
     * @param ComparableCollectionInterface $to
     * @param bool                          $strict
     * @param bool                          $keepOriginalOrder
     * @param callable|null                 $recordChange If provided, it is a method that will be called when it needs
     *                                                    to store the addition, removal or difference of an element.
     *                                                    The callable will be:
     *                                                    function (self::ADDED, $key, $element),
     *                                                    function (self::REMOVED, $key, $element) or
     *                                                    function (self::CHANGED, $key, $fromElement, $toElement)
     */
    public function __construct(
        ComparableCollectionInterface $from,
        ComparableCollectionInterface $to,
        bool $strict = false,
        bool $keepOriginalOrder = false,
        ?callable $recordChange = null
    ) {
        if ($from->isEmpty() && $to->isEmpty()) {
            return;
        }

        $comparisonMethod = $strict ? ComparableInterface::IS_IDENTICAL_TO : ComparableInterface::IS_SAME_AS;

        /** @var ComparableInterface[] $fromElements */
        $fromElements = $from->toArray();

        /** @var ComparableInterface[] $toElements */
        $toElements = $to->toArray();

        foreach ($toElements as $key => $element) {
            if (array_key_exists($key, $fromElements)) {
                if ($element->compareTo($comparisonMethod, $fromElements[$key])) {
                    // Key exists and it is the same

                    $this->changes[$key] = [self::SOURCE, $key];

                    unset($fromElements[$key]);
                } else {
                    $this->numberChanges++;

                    if (false !== ($search = $from->search($element, $strict))) {
                        // Is there, but has another key

                        $this->changes[$key] = [self::SOURCE, $search];

                        unset($fromElements[$search]);
                    } elseif (false !== $to->search($fromElements[$key], $strict)) {
                        // It is not elsewhere but the one with that index is needed later on (this element is new)

                        $this->changes[$key] = [
                            self::ADDED,
                            null === $recordChange ? $element : $recordChange(self::ADDED, $key, $element),
                        ];
                    } else {
                        // It is not elsewhere, it has changed

                        $this->changes[$key] = [
                            self::CHANGED,
                            null === $recordChange
                                ? $element
                                : $recordChange(self::CHANGED, $key, $element, $fromElements[$key]),
                        ];

                        unset($fromElements[$key]);
                    }
                }
            } elseif (false !== ($search = $from->search($element, $strict))) {
                // Is there, but has another key

                $this->changes[$key] = [self::SOURCE, $search];

                unset($fromElements[$search]);

                $this->numberChanges++;
            } else {
                // It is not there, it is new

                $this->changes[$key] = [
                    self::ADDED,
                    null === $recordChange ? $element : $recordChange(self::ADDED, $key, $element),
                ];

                $this->numberChanges++;
            }
        }

        if (!empty($fromElements)) {
            foreach ($fromElements as $key => $element) {
                $this->changes[$key] = [
                    self::REMOVED,
                    null === $recordChange ? true : $recordChange(self::REMOVED, $key, $element),
                ];
            }

            $this->numberChanges += count($fromElements);
        }
    }

    public function getChangesCount(): int
    {
        return $this->numberChanges;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Applies this Diff over the collection provided and returns a new one with it applied
     */
    public function apply(
        ComparableCollectionInterface $sourceCollection,
        bool $keepOriginalOrder = false
    ): ComparableCollectionInterface {
        $collectionClass = get_class($sourceCollection);
        $source = $sourceCollection->toArray();
        $array = [];

        foreach ($this->changes as $key => [$action, $item]) {
            if (self::SOURCE === $action) {
                if ($item !== $key) {
                    $array[$key] = $sourceCollection[$item];
                }
            } elseif (self::ADDED === $action) {
                $array[$key] = $item;
            } elseif (self::REMOVED === $action) {
                unset($array[$key]);
            } else {
                $array[$key] = $item;
            }
        }

        if ($keepOriginalOrder) {
            $array = array_merge($this->changes, $source);
        }

        return new $collectionClass($array);
    }
}
