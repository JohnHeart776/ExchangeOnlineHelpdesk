<?php

/**
 * Base trait for entity relationship functionality
 * Provides common patterns for has/get entity relationships used across multiple traits
 */
trait EntityRelationshipTrait
{
    /**
     * Generic method to check if an entity exists based on ID
     * 
     * @param string $idMethodName The method name to get the ID (e.g., 'getUserId', 'getMailId')
     * @return bool
     */
    protected function hasEntityById(string $idMethodName): bool
    {
        if (!method_exists($this, $idMethodName)) {
            return false;
        }
        
        $id = $this->$idMethodName();
        return is_numeric($id) && (int)$id > 0;
    }
    
    /**
     * Generic method to get an entity by ID
     * 
     * @param string $entityClass The entity class name (e.g., 'User', 'Mail')
     * @param string $idMethodName The method name to get the ID
     * @return mixed|null
     */
    protected function getEntityById(string $entityClass, string $idMethodName)
    {
        if (!$this->hasEntityById($idMethodName)) {
            return null;
        }
        
        $id = $this->$idMethodName();
        return new $entityClass($id);
    }
    
    /**
     * Generic method to get an entity ID as integer
     * 
     * @param string $idMethodName The base method name (e.g., 'getUserId')
     * @return int
     */
    protected function getIdAsInt(string $idMethodName): int
    {
        if (!method_exists($this, $idMethodName)) {
            return 0;
        }
        
        return (int)$this->$idMethodName();
    }
    
    /**
     * Generic method to link/associate an entity by updating foreign key
     * 
     * @param string $fieldName The database field name to update
     * @param mixed $entity The entity to link
     * @param string $entityIdProperty The property name of the entity's ID
     * @return static
     */
    protected function linkEntity(string $fieldName, $entity, string $entityIdProperty): static
    {
        if (property_exists($entity, $entityIdProperty)) {
            $this->update($fieldName, $entity->$entityIdProperty);
            $this->spawn();
        }
        
        return $this;
    }
}