<?php declare(strict_types=1);

namespace Aircury\Collection;

class CollectionDiff
{
    public const SOURCE = 0;
    public const ADDED = 1;
    public const REMOVED = 2;
    public const CHANGED = 3;

    /**
     * 'destinationKey' => [action, item]. The item is the source key for SOURCE, and for the other actions
     * the element (or true for REMOVED), or whatever $recordChange returned for it.
     *
     * @var array<array-key, array{int, mixed}>
     */
    private array $changes = [];

    private int $numberChanges = 0;

    /**
     * @param bool          $keepOriginalOrder Unused. Kept so that callers passing $recordChange by position still work.
     * @param callable|null $recordChange      If provided, it is a method that will be called when it needs
     *                                         to store the addition, removal or difference of an element.
     *                                         The callable will be:
     *                                         function (self::ADDED, $key, $element),
     *                                         function (self::REMOVED, $key, $element) or
     *                                         function (self::CHANGED, $key, $toElement, $fromElement)
     *
     * @phpstan-ignore constructor.unusedParameter (public signature kept for backwards compatibility, see $keepOriginalOrder)
     */
    public function __construct(
        ComparableCollectionInterface $from,
        ComparableCollectionInterface $to,
        bool $strict = false,
        bool $keepOriginalOrder = false,
        ?callable $recordChange = null
    ) {
        $comparisonMethod = $strict ? ComparableInterface::IS_IDENTICAL_TO : ComparableInterface::IS_SAME_AS;
        // Source elements not matched yet: whatever is left at the end was removed
        $fromElements = self::comparableElements($from);

        foreach (self::comparableElements($to) as $key => $element) {
            if (array_key_exists($key, $fromElements) && $element->compareTo($comparisonMethod, $fromElements[$key])) {
                // Key exists and it is the same
                $this->changes[$key] = [self::SOURCE, $key];

                unset($fromElements[$key]);

                continue;
            }

            $this->changes[$key] = $this->diffElement($key, $element, $fromElements, $from, $to, $strict, $recordChange);
            $this->numberChanges++;
        }

        foreach ($fromElements as $key => $element) {
            $this->changes[$key] = [self::REMOVED, self::record($recordChange, true, self::REMOVED, $key, $element)];
        }

        $this->numberChanges += \count($fromElements);
    }

    /**
     * @return array<array-key, ComparableInterface>
     */
    private static function comparableElements(ComparableCollectionInterface $collection): array
    {
        // A comparable collection only accepts comparable elements (see AbstractComparableCollection)
        return array_filter(
            $collection->toArray(),
            static fn(object $element): bool => $element instanceof ComparableInterface,
        );
    }

    /**
     * Works out the change for an element of $to that is not the same as the source element with its key.
     *
     * @param array<array-key, ComparableInterface> $fromElements Unmatched source elements. The one matched is removed.
     *
     * @return array{int, mixed}
     */
    private function diffElement(
        int|string $key,
        ComparableInterface $element,
        array &$fromElements,
        ComparableCollectionInterface $from,
        ComparableCollectionInterface $to,
        bool $strict,
        ?callable $recordChange
    ): array {
        $search = $from->search($element, $strict);

        if (\is_int($search) || \is_string($search)) {
            // Is there, but has another key
            unset($fromElements[$search]);

            return [self::SOURCE, $search];
        }

        if (!array_key_exists($key, $fromElements) || false !== $to->search($fromElements[$key], $strict)) {
            // It is new, or the source element with this key is needed elsewhere (so this element is new)
            return [self::ADDED, self::record($recordChange, $element, self::ADDED, $key, $element)];
        }

        // It is not elsewhere, it has changed
        $change = [self::CHANGED, self::record($recordChange, $element, self::CHANGED, $key, $element, $fromElements[$key])];

        unset($fromElements[$key]);

        return $change;
    }

    /**
     * Returns what $recordChange returns for the change, or $default when there is no $recordChange.
     */
    private static function record(?callable $recordChange, mixed $default, int $action, int|string $key, ComparableInterface ...$elements): mixed
    {
        return null === $recordChange ? $default : $recordChange($action, $key, ...$elements);
    }

    public function getChangesCount(): int
    {
        return $this->numberChanges;
    }

    /**
     * @return array<array-key, array{int, mixed}>
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Applies this Diff over the collection provided and returns a new one with it applied.
     * Only meaningful for a diff built without $recordChange, as it needs the elements themselves.
     *
     * @param bool $keepOriginalOrder Keep the elements that were already in the source in their original order,
     *                                followed by the new ones. Otherwise, the order of the destination is used.
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
                $array[$key] = $sourceCollection[$item];
            } elseif (self::REMOVED !== $action) {
                $array[$key] = $item;
            }
        }

        if ($keepOriginalOrder) {
            $array = array_replace(array_intersect_key($source, $array), $array);
        }

        return new $collectionClass($array);
    }
}
