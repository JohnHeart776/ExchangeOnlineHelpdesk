<?php

namespace Controller\Base;

/**
 * Base controller class providing common CRUD operations
 * Eliminates duplication across all controller classes
 */
abstract class BaseController
{
    /**
     * Get the entity class name that this controller manages
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Get the database table name for this entity
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * Get the primary key field name
     * @return string
     */
    protected function getPrimaryKeyField(): string
    {
        return $this->getEntityClass() . 'Id';
    }

    /**
     * Get all entities with pagination and sorting
     * 
     * @param int $limit
     * @param string|null $direction
     * @param string|null $sortBy
     * @return array
     * @throws \Database\DatabaseQueryException
     */
    public function getAll(int $limit = 50, ?string $direction = 'DESC', ?string $sortBy = null): array
    {
        global $d;
        
        $tableName = $this->getTableName();
        $primaryKey = $this->getPrimaryKeyField();
        $entityClass = $this->getEntityClass();
        
        // Default sort by primary key if not specified
        if (!$sortBy) {
            $sortBy = $primaryKey;
        }
        
        // Validate direction
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        
        $_q = "SELECT {$primaryKey} FROM {$tableName} ORDER BY {$sortBy} {$direction} LIMIT :limit";
        $t = $d->getPDO($_q, ['limit' => $limit]);
        
        $r = [];
        foreach ($t as $u) {
            $r[] = new $entityClass((int)$u[$primaryKey]);
        }
        
        return $r;
    }

    /**
     * Get entity by ID
     * 
     * @param int $id
     * @return mixed|null
     * @throws \Database\DatabaseQueryException
     */
    public function getById(int $id)
    {
        $entityClass = $this->getEntityClass();
        $primaryKey = $this->getPrimaryKeyField();
        
        return $this->searchOneBy($primaryKey, (string)$id);
    }

    /**
     * Get entity by GUID
     * 
     * @param string $guid
     * @return mixed|null
     * @throws \Database\DatabaseQueryException
     */
    public function getByGuid(string $guid)
    {
        return $this->searchOneBy('Guid', $guid);
    }

    /**
     * Search for one entity by field and term
     * 
     * @param string $field
     * @param string $term
     * @return mixed|null
     * @throws \Database\DatabaseQueryException
     */
    public function searchOneBy(string $field, string $term)
    {
        $results = $this->searchBy($field, $term, true, 1);
        return empty($results) ? null : $results[0];
    }

    /**
     * Search for entities by field and term
     * 
     * @param string $field
     * @param string $term
     * @param bool $fetchOne
     * @param int $limit
     * @return array
     * @throws \Database\DatabaseQueryException
     */
    public function searchBy(string $field, string $term, bool $fetchOne = false, int $limit = 50): array
    {
        global $d;
        
        $tableName = $this->getTableName();
        $primaryKey = $this->getPrimaryKeyField();
        $entityClass = $this->getEntityClass();
        
        $limitClause = $fetchOne ? 'LIMIT 1' : "LIMIT {$limit}";
        $_q = "SELECT {$primaryKey} FROM {$tableName} WHERE {$field} = :term {$limitClause}";
        
        $t = $d->getPDO($_q, ['term' => $term]);
        
        $r = [];
        foreach ($t as $u) {
            $r[] = new $entityClass((int)$u[$primaryKey]);
        }
        
        return $r;
    }

    /**
     * Check if entity exists by field and term
     * 
     * @param string $field
     * @param string $term
     * @return bool
     * @throws \Database\DatabaseQueryException
     */
    public function exist(string $field, string $term): bool
    {
        global $d;
        
        $tableName = $this->getTableName();
        $_q = "SELECT COUNT(*) as count FROM {$tableName} WHERE {$field} = :term LIMIT 1";
        
        $result = $d->getPDO($_q, ['term' => $term]);
        return (int)$result['count'] > 0;
    }

    /**
     * Get random entities
     * 
     * @param int $amount
     * @return array
     * @throws \Database\DatabaseQueryException
     */
    public function getRandom(int $amount = 5): array
    {
        global $d;
        
        $tableName = $this->getTableName();
        $primaryKey = $this->getPrimaryKeyField();
        $entityClass = $this->getEntityClass();
        
        $_q = "SELECT {$primaryKey} FROM {$tableName} ORDER BY RAND() LIMIT :amount";
        $t = $d->getPDO($_q, ['amount' => $amount]);
        
        $r = [];
        foreach ($t as $u) {
            $r[] = new $entityClass((int)$u[$primaryKey]);
        }
        
        return $r;
    }

    /**
     * Save entity (create or update)
     * 
     * @param mixed $obj
     * @return mixed|null
     * @throws \Database\DatabaseQueryException
     */
    public function save($obj)
    {
        if (!$obj->isValid()) {
            return null;
        }
        
        $primaryKeyMethod = 'get' . $this->getPrimaryKeyField();
        $isUpdate = method_exists($obj, $primaryKeyMethod) && $obj->$primaryKeyMethod() > 0;
        
        if ($isUpdate) {
            return $obj->update() ? $obj : null;
        } else {
            return $obj->create() ? $obj : null;
        }
    }
}