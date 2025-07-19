<?php

namespace Controller\Base;

use Core\InputValidator;
use Database\Database;

/**
 * Base controller for update endpoints to eliminate code duplication
 * Provides common functionality for entity update operations
 */
abstract class BaseUpdateController
{
    protected Database $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Abstract method to get the entity class name
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Abstract method to get the entity not found message
     * @return string
     */
    abstract protected function getEntityNotFoundMessage(): string;

    /**
     * Handles the update operation
     * @return void
     */
    public function handleUpdate(): void
    {
        // Validate required parameters
        $errors = InputValidator::validateRequiredPost(['pk', 'name', 'value']);
        if (!empty($errors)) {
            $this->sendErrorResponse('Missing required parameters: ' . implode(', ', $errors));
            return;
        }

        // Get and validate primary key
        $pk = InputValidator::postParam('pk', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($pk)) {
            $this->sendErrorResponse('Invalid primary key');
            return;
        }

        // Create entity instance
        $entityClass = $this->getEntityClass();
        try {
            $entity = new $entityClass($pk);
        } catch (\Exception $e) {
            $this->sendErrorResponse('Failed to create entity: ' . $e->getMessage());
            return;
        }

        // Validate entity exists
        if (!$entity->isValid()) {
            $this->sendErrorResponse($this->getEntityNotFoundMessage());
            return;
        }

        // Get update parameters
        $name = InputValidator::postParam('name', FILTER_SANITIZE_SPECIAL_CHARS);
        $value = InputValidator::postParam('value', FILTER_SANITIZE_SPECIAL_CHARS);
        $action = InputValidator::postParam('action', FILTER_SANITIZE_SPECIAL_CHARS);

        // Perform update operation
        try {
            if ($action === 'toggle') {
                $updateResult = $entity->toggleValue($name);
            } else {
                $updateResult = $entity->update($name, $value);
            }

            $this->sendSuccessResponse($updateResult, $entity);
        } catch (\Exception $e) {
            $this->sendErrorResponse('Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Sends success response
     * @param bool $result Update operation result
     * @param object $entity Updated entity
     * @return void
     */
    protected function sendSuccessResponse(bool $result, object $entity): void
    {
        header('Content-Type: application/json');
        echo jsonStatus($result, '', $entity->toJsonObject());
    }

    /**
     * Sends error response
     * @param string $message Error message
     * @return void
     */
    protected function sendErrorResponse(string $message): void
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo jsonStatus(false, $message);
    }

    /**
     * Static method to handle update with authentication check
     * @param string $entityClass
     * @param string $notFoundMessage
     * @return void
     */
    public static function handleAuthenticatedUpdate(string $entityClass, string $notFoundMessage): void
    {
        // Check authentication
        \Login::requireIsAgent();

        // Create anonymous controller instance
        $controller = new class($entityClass, $notFoundMessage) extends BaseUpdateController {
            private string $entityClass;
            private string $notFoundMessage;

            public function __construct(string $entityClass, string $notFoundMessage)
            {
                parent::__construct();
                $this->entityClass = $entityClass;
                $this->notFoundMessage = $notFoundMessage;
            }

            protected function getEntityClass(): string
            {
                return $this->entityClass;
            }

            protected function getEntityNotFoundMessage(): string
            {
                return $this->notFoundMessage;
            }
        };

        $controller->handleUpdate();
    }
}