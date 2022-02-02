<?php namespace Sarok;

use Sarok\Exceptions\DIException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class DIContainer {
    private const NAMESPACE_SEPARATOR = '\\';

    private $instances = array();

    // Removes namespace prefix and converts CamelCase to lowerCamelCase
    private function getKey(string $name): string {
        if ($lastSeparator = strrpos($name, self::NAMESPACE_SEPARATOR)) {
            $name = substr($name, $lastSeparator + 1);
        }

        return lcfirst($name);
    }

    public function put(string $name, $obj) {
        $key = $this->getKey($name);

        if ($obj === null) {
            unset($this->instances[$key]);
        } else {
            $this->instances[$key] = $obj;
        }
    }

    public function getOptional(string $name) {
        $key = $this->getKey($name);

        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        } else {
            return null;
        }
    }

    public function get(string $name, bool $prototype = false) {
        if (!$prototype) {
            // If we already have an instance for the key, return it
            $instance = $this->getOptional($name);
            if ($instance !== null) {
                return $instance;
            }
        }

        if (!class_exists($name)) {
            throw new DIException("Requested class '$name' does not exist.");
        }

        $reflection = new ReflectionClass($name);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            // No constructor found on class, instantiate it without any parameters
            try {
                $instance = $reflection->newInstance();
            } catch (ReflectionException $e) {
                throw new DIException("Failed to create new instance of '$name'.", null, $e);
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

                // If we still don't have something, that is a problem.
                if ($value === null) {
                    throw new DIException("Couldn't find value to inject for parameter '$paramType $paramName' of class '$name'.");
                }

                $values[] = $value;
            }

            // Construct using resolved parameters
            try {
                $instance = $reflection->newInstanceArgs($values);
            } catch (ReflectionException $e) {
                throw new DIException("Failed to create new instance of '$name' with arguments $values.", null, $e);
            }
        }

        // Register instance for future reference if not in prototype scope
        if (!$prototype) {
            $key = $this->getKey($name);
            $this->put($key, $instance);
        }

        return $instance;
    }
}
