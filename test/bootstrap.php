<?php
/**
 * PHPUnit Bootstrap File for Exchange Online Helpdesk Tests
 */

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define test environment
define('TESTING', true);

// Set up paths
define('TEST_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(__DIR__));
define('SRC_ROOT', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'src');

// Include the main application bootstrap if it exists
$mainBootstrap = SRC_ROOT . DIRECTORY_SEPARATOR . 'bootstrap.php';
if (file_exists($mainBootstrap)) {
    require_once $mainBootstrap;
}

// Set up autoloading for test classes
spl_autoload_register(function ($className) {
    // Handle test classes
    if (strpos($className, 'Test\\') === 0) {
        $file = TEST_ROOT . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 5)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle source classes
    $file = SRC_ROOT . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.class.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

// Set up test database configuration (if needed)
// This would typically use a separate test database
$testConfig = [
    'db_host' => 'localhost',
    'db_name' => 'helpdesk_test',
    'db_user' => 'test_user',
    'db_pass' => 'test_pass'
];

// Make test configuration available globally
$GLOBALS['test_config'] = $testConfig;

// Set timezone for consistent testing
date_default_timezone_set('UTC');