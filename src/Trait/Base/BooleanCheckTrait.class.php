<?php

/**
 * Base trait for boolean check functionality
 * Provides common patterns for boolean checks used across multiple traits
 */
trait BooleanCheckTrait
{
    /**
     * Generic method to check if a boolean field is true based on integer value
     * 
     * @param string $methodName The method name to get the integer value (e.g., 'getIsOpenAsInt')
     * @return bool
     */
    protected function isBooleanFieldTrue(string $methodName): bool
    {
        if (!method_exists($this, $methodName)) {
            return false;
        }
        
        return (int)$this->$methodName() > 0;
    }
    
    /**
     * Generic method to check if a string field equals a specific value
     * 
     * @param string $methodName The method name to get the string value
     * @param string $expectedValue The expected value to compare against
     * @return bool
     */
    protected function isFieldEqualTo(string $methodName, string $expectedValue): bool
    {
        if (!method_exists($this, $methodName)) {
            return false;
        }
        
        return $this->$methodName() === $expectedValue;
    }
    
    /**
     * Generic method to check if an entity has a specific property/relationship
     * 
     * @param string $methodName The method name to get the entity or ID
     * @return bool
     */
    protected function hasProperty(string $methodName): bool
    {
        if (!method_exists($this, $methodName)) {
            return false;
        }
        
        $value = $this->$methodName();
        
        // Check for numeric ID
        if (is_numeric($value)) {
            return (int)$value > 0;
        }
        
        // Check for non-null object/string
        return $value !== null && $value !== '';
    }
    
    /**
     * Generic method to check if a text type matches expected value
     * 
     * @param string $expectedType The expected type (e.g., 'txt', 'html')
     * @return bool
     */
    protected function isTextType(string $expectedType): bool
    {
        if (!method_exists($this, 'getTextType')) {
            return false;
        }
        
        return $this->getTextType() === $expectedType;
    }
}