<?php declare(strict_types=1);

namespace Sarok;

use Sarok\Exceptions\DIException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

final class DIContainer 
{
    private const NAMESPACE_SEPARATOR = '\\';

    /** @var array<string, mixed> */
    private $instances = array();

    private function getKey(string $name) : string 
    {
        // Remove namespace prefix if present
        $lastSeparator = strrpos($name, self::NAMESPACE_SEPARATOR);
        if ($lastSeparator !== false) {
            $name = substr($name, $lastSeparator + 1);
        }

        // Convert CamelCased names to lowerCamelCase
        return lcfirst($name);
    }

    /** 
     * Registers an instance using the specified symbolic name.
     */ 
    public function put(string $name, mixed $obj) : void
    {
        $key = $this->getKey($name);

        if ($obj === null) {
            unset($this->instances[$key]);
        } else {
            $this->instances[$key] = $obj;
        }
    }

    /**
     * Looks up an instance by symbolic name, returning null if it is not already registered.
     */
    public function getOptional(string $name) : mixed 
    {
        $key = $this->getKey($name);

        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        } else {
            return null;
        }
    }

    /**
     * Looks up an instance by symbolic name, creating a new instance if it is not already registered
     * and not in prototype mode (the default is singleton mode). Constructor parameters are injected
     * from the container during instance creation.
     * 
     * If you want to use singleton dependencies for a prototype instance, make sure to register or
     * instantiate those first! The prototype flag is propagated for instances created as part of
     * constructor injection.
     */
    public function get(string $name, bool $prototype = false) : mixed 
    {
        if ($prototype === false) {
            // If we already have a singleton instance for the key, return it
            $instance = $this->getOptional($name);
            if ($instance !== null) {
                return $instance;
            }
        }

        if (!class_exists($name)) {
            throw new DIException("Requested class '${name}' does not exist.");
        }

        $reflection = new ReflectionClass($name);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            // No constructor found on class, instantiate it without any parameters
            try {
                $instance = $reflection->newInstance();
            } catch (ReflectionException $e) {
                throw new DIException("Failed to create new instance of '${name}' using 0-arg constructor.", 0, $e);
            }
        } else {
            // Inject dependencies via constructor parameters
            $parameters = $constructor->getParameters();
            $values = array();

            foreach ($parameters as $param) {
                $value = null;

                // First attempt: find via parameter name (no injection)
                $paramType = $param->getType();
                $paramName = $param->getName();
                $value = $this->getOptional($paramName);

                // Second attempt: find via type name, if it is not a built-in type (apply injection)
                if ($value === null) {
                    if ($paramType instanceof ReflectionNamedType) {
                        if (!$paramType->isBuiltin()) {
                            // XXX: Scope is propagated here
                            $value = $this->get($paramType->getName(), $prototype);
                        }
                    }
                }

                // Third attempt: if the value is optional, use the default value
                if ($value === null) {
                    if ($param->isOptional()) {
                        $value = $param->getDefaultValue();
                    }
                }

                // If we still don't have any value, that is a problem.
                if ($value === null) {
                    throw new DIException("Couldn't find value to inject for parameter '${paramType} ${paramName}' of class '${name}'.");
                }

                $values[] = $value;
            }

            // Construct using resolved parameters
            try {
                $instance = $reflection->newInstanceArgs($values);
            } catch (ReflectionException $e) {
                throw new DIException("Failed to create new instance of '${name}' with arguments ${values}.", 0, $e);
            }
        }

        // Register instance for future reference if not in prototype scope
        if ($prototype === false) {
            $this->put($name, $instance);
        }

        return $instance;
    }
}
