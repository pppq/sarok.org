<?php declare(strict_types=1);

namespace sarok\models;

use UnexpectedValueException;

/**
 * An array that keeps track of membership changes and does not allow 
 * duplicate values. Since we are using values as associative array keys, 
 * only integers and strings are allowed.
 */
class TrackedSet
{
    private const REMOVED = -1;
    private const UNCHANGED = 0;
    private const ADDED = 1;

    /**
     * @var array<int|string, int>
     */
    private array $values = array();

    /**
     * Resets changes and sets all content to match the given array.
     * 
     * @param array<int|string> $values
     */
    public function setAll(array $values = array()) : void
    {
        // Initialize all elements to unchanged
        $this->values = array_map(fn($v) => self::UNCHANGED, $values);
    }
    
    public function add(int|string $value) : bool
    {
        if (!isset($this->values[$value])) {
            // This element was not part of the array; register it as added
            $this->values[$value] = self::ADDED;
            return true;
        }

        $state = $this->values[$value];

        if ($state === self::UNCHANGED || $state === self::ADDED) {
            // Element is unchanged or added, don't add it twice
            return false;
        }

        /* 
         * Element was removed, then reinstated, so now it is unchanged from the starting
         * set's point of view. The return value indicates whether the set itself changed, 
         * though, so return true here as well.
         */
        $this->values[$value] = self::UNCHANGED;
        return true;
    }

    public function remove(int|string $value) : bool
    {
        if (!isset($this->values[$value])) {
            // This element was not part of the array
            return false;
        }

        $state = $this->values[$value];

        switch ($state) {
            case self::REMOVED:
                // Element is already removed
                return false;
            
            case self::UNCHANGED:
                // Element was there from the beginning, now we are removing it
                $this->values[$value] = self::REMOVED;
                return true;
            
            case self::ADDED:
                // Element was added, but now it is removed (entirely)
                unset($this->values[$value]);
                return true;

            default:
                throw new UnexpectedValueException("Unexpected element state {$state} for value {$value}");
        }
    }

    public function toArray() : array
    {
        // Unchanged and added keys are the current members of this set
        return array_keys(array_filter($this->values, fn($v) => ($v === self::UNCHANGED || $v === self::ADDED)));
    }

    private function valuesWithState(int $state) {
        return array_keys(array_filter($this->values, fn($v) => ($v === $state)));
    }

    public function addedValues() {
        return $this->valuesWithState(self::ADDED);
    }

    public function removedValues() {
        return $this->valuesWithState(self::REMOVED);
    }

    public function flush() : array
    {
        // Actually remove elements marked as removed; the rest of the elements are marked as unchanged
        foreach ($this->values as $value => $state) {
            if ($state === self::REMOVED) {
                unset($this->values[$value]);
            } else {
                $this->values[$value] = self::UNCHANGED;
            }
        }

        // Return the surviving elements as an array
        return array_keys($this->values);
    }
}
