<?php

use Controller\Base\BaseController;

/**
 * ActionGroup Controller (Refactored Version)
 * Demonstrates the refactoring pattern using BaseController
 * 
 * BEFORE: 214 lines with duplicate CRUD methods
 * AFTER: 35 lines with inherited functionality
 * 
 * This shows how all controllers can be refactored to eliminate duplication.
 * All common CRUD methods are inherited from BaseController and work automatically.
 */
class ActionGroupControllerRefactored extends BaseController
{
    /**
     * Get the entity class name that this controller manages
     * @return string
     */
    protected function getEntityClass(): string
    {
        return 'ActionGroup';
    }

    /**
     * Get the database table name for this entity
     * @return string
     */
    protected function getTableName(): string
    {
        return 'ActionGroup';
    }

    // All CRUD methods are now inherited from BaseController:
    // - getAll(int $limit, ?string $direction, ?string $sortBy): array
    // - getById(int $id): ?ActionGroup
    // - getByGuid(string $guid): ?ActionGroup
    // - searchOneBy(string $field, string $term): ?ActionGroup
    // - searchBy(string $field, string $term, bool $fetchOne, int $limit): array
    // - exist(string $field, string $term): bool
    // - getRandom(int $amount): array
    // - save(ActionGroup $obj): ?ActionGroup
    
    // Any controller-specific methods can be added here if needed
}