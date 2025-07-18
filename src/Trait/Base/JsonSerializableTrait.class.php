<?php

/**
 * Base trait for JSON serialization functionality
 * Provides common JSON serialization helper methods used across multiple traits
 */
trait JsonSerializableTrait
{
    /**
     * Get common base fields for JSON serialization
     * 
     * @return array
     */
    protected function getBaseJsonFields(): array
    {
        $baseFields = [];

        // Add GUID if available
        if (method_exists($this, 'getGuid')) {
            $baseFields['guid'] = $this->getGuid();
        }

        // Add created datetime if available
        if (method_exists($this, 'getCreatedDatetimeAsDateTime')) {
            $baseFields['created'] = $this->getCreatedDatetimeAsDateTime()->format("Y-m-d H:i:s");
        }

        return $baseFields;
    }

    /**
     * Helper method to safely get a related entity's JSON representation
     * 
     * @param mixed $entity
     * @return array|null
     */
    protected function entityToJson($entity): ?array
    {
        if ($entity === null) {
            return null;
        }

        if (method_exists($entity, 'toJsonObject')) {
            return $entity->toJsonObject();
        }

        return null;
    }
}
