<?php declare(strict_types=1);

namespace sarok\models;

use ArrayAccess;
use DomainException;

/**
 * An associative array that keeps track of modified keys.
 */
class TrackedMap implements ArrayAccess
{
    /** 
     * @var array<string, mixed> 
     */
    private array $values;

    /**
     * @var array<string, bool>
     */
    private array $modifiedKeys = array();

    public function __construct(array $values = array()) {
        $this->values = $values;
    }

    public function offsetExists(mixed $offset) : bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet(mixed $offset) : mixed
    {
        // XXX: Unset keys will return an empty string which is inconsistent with offsetExists
        return $this->get($offset, '');
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        // Array operator is not interested in the value that is being pushed out
        $this->put($offset, $value);
    }

    public function offsetUnset(mixed $offset) : void
    {
        throw new DomainException('Unsetting array elements is not supported.');
    }

    /**
     * Resets modified keys to an empty array and sets all content to match the given associative array.
     * 
     * @param array<string, mixed> $values
     */
    public function putAll(array $values = array()) : void
    {
        $this->values = $values;
        $this->modifiedKeys = array();
    }
    
    /**
     * Sets a single key to the specified value, and marks the key as modified if the
     * new value differs from the earlier one.
     * 
     * @param string $key the array key
     * @param mixed $value the new value
     * @return mixed the previously set value, or `null` if no value was set earlier
     */
    public function put(string $key, mixed $value) : mixed
    {
        // Returns the previous value if one was already set
        if (!$this->offsetExists($key)) {
            $oldValue = null;
        } else {
            $oldValue = $this->values[$key];
        }
        
        // TODO: A => B => A style changes will still be marked as modified
        if ($value !== $oldValue) {
            $this->values[$key] = $value;
            $this->modifiedKeys[$key] = true;
        }
    
        return $oldValue;
    }
    
    /**
     * Retrieves value by key, or uses the provided default value if the key
     * was not set.
     * 
     * @param string $key the key of the user property to retrieve
     * @param mixed $defaultValue the default value to use
     * @return mixed the property value, or the provided default value if no value was set earlier
     */
    public function get(string $key, mixed $defaultValue = null) : mixed
    {
        if (!$this->offsetExists($key)) {
            return $defaultValue;
        } else {
            return $this->values[$key];
        }
    }
    
    /**
     * Returns an array containing key-value pairs for keys that were marked as modified,
     * then clears modified keys.
     * 
     * @return array<string, mixed> the key-value pairs that should be updated
     */
    public function flush() : array
    {
        $modifiedData = array_intersect_key($this->values, $this->modifiedKeys);
        $this->modifiedKeys = array();
        return $modifiedData;
    }

    public function toArray() : array
    {
        return $this->values;
    }
}
