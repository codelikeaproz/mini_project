<?php

declare(strict_types=1);

namespace App\DTOs;

use JsonSerializable;

/**
 * Base Data Transfer Object
 *
 * Provides common functionality for all DTOs:
 * - Immutable data objects
 * - Type safety
 * - Validation
 * - Serialization
 */
abstract class BaseDTO implements JsonSerializable
{
    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): static
    {
        $instance = new static();
        $instance->populate($data);
        $instance->validate();

        return $instance;
    }

    /**
     * Populate DTO properties from array
     */
    protected function populate(array $data): void
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            if (array_key_exists($propertyName, $data)) {
                $this->$propertyName = $data[$propertyName];
            }
        }
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $data = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $data[$propertyName] = $this->$propertyName;
        }

        return $data;
    }

    /**
     * JSON serialization
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Validate DTO data
     * Override in child classes for specific validation
     */
    protected function validate(): void
    {
        // Base validation logic
        $this->validateRequired();
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(): void
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Check if property has a type declaration
            $type = $property->getType();

            if ($type && !$type->allowsNull() && !isset($this->$propertyName)) {
                throw new \InvalidArgumentException("Required property '{$propertyName}' is missing");
            }
        }
    }

    /**
     * Validate string length
     */
    protected function validateStringLength(string $field, int $maxLength, int $minLength = 0): void
    {
        $value = $this->$field ?? '';
        $length = strlen($value);

        if ($length < $minLength) {
            throw new \InvalidArgumentException("Field '{$field}' must be at least {$minLength} characters");
        }

        if ($length > $maxLength) {
            throw new \InvalidArgumentException("Field '{$field}' cannot exceed {$maxLength} characters");
        }
    }

    /**
     * Validate email format
     */
    protected function validateEmail(string $field): void
    {
        $value = $this->$field ?? '';

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Field '{$field}' must be a valid email address");
        }
    }

    /**
     * Validate enum value
     */
    protected function validateEnum(string $field, array $allowedValues): void
    {
        $value = $this->$field ?? '';

        if (!in_array($value, $allowedValues)) {
            $allowed = implode(', ', $allowedValues);
            throw new \InvalidArgumentException("Field '{$field}' must be one of: {$allowed}");
        }
    }

    /**
     * Validate date format
     */
    protected function validateDate(string $field, string $format = 'Y-m-d H:i:s'): void
    {
        $value = $this->$field ?? '';

        $date = \DateTime::createFromFormat($format, $value);

        if (!$date || $date->format($format) !== $value) {
            throw new \InvalidArgumentException("Field '{$field}' must be a valid date in format {$format}");
        }
    }

    /**
     * Validate numeric range
     */
    protected function validateNumericRange(string $field, float $min, float $max): void
    {
        $value = $this->$field ?? 0;

        if ($value < $min || $value > $max) {
            throw new \InvalidArgumentException("Field '{$field}' must be between {$min} and {$max}");
        }
    }

    /**
     * Convert to JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Create DTO from JSON string
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return static::fromArray($data);
    }

    /**
     * Clone DTO with updated data
     */
    public function with(array $updates): static
    {
        $data = array_merge($this->toArray(), $updates);
        return static::fromArray($data);
    }
}
