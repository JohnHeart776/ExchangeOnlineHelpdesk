<?php
require_once __DIR__ . '/../../src/bootstrap.php';

use Controller\Base\BaseUpdateController;

// Use the base controller to handle the update operation
BaseUpdateController::handleAuthenticatedUpdate('MenuItem', 'Menüelement not found.');
