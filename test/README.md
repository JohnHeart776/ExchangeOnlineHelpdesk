# Exchange Online Helpdesk - Test Suite

This directory contains the test suite for the Exchange Online Helpdesk application.

## Structure

```
test/
├── bootstrap.php          # PHPUnit bootstrap file
├── phpunit.xml           # PHPUnit configuration
├── Unit/                 # Unit tests
│   ├── Database/
│   │   └── DatabaseTest.php
│   ├── Client/
│   │   └── GraphClientTest.php
│   └── Core/
│       ├── DateHelperTest.php
│       └── GuidHelperTest.php
├── Integration/          # Integration tests
└── README.md            # This file
```

## Test Categories

### Unit Tests
Unit tests focus on testing individual classes and methods in isolation. They use mocking to avoid dependencies on external systems.

**Implemented:**
- `DatabaseTest.php` - Tests for the Database class including singleton pattern, exception handling, PDO operations, and connection management
- `GraphClientTest.php` - Tests for the GraphClient class focusing on constructor behavior and public interface validation
- `DateHelperTest.php` - Comprehensive tests for DateHelper static methods including date parsing, MySQL formatting, and edge cases
- `GuidHelperTest.php` - Tests for GUID generation including format validation, uniqueness, UUID v4 compliance, and performance

### Integration Tests
Integration tests verify the interaction between multiple components and may require external dependencies like databases or APIs.

**To be implemented:**
- Database integration tests with actual database connections
- GraphClient integration tests with Microsoft Graph API
- Controller integration tests with database operations
- End-to-end workflow tests

## Running Tests

### Prerequisites
- PHP 7.4 or higher
- PHPUnit 9.5 or higher
- Composer (for dependency management)

### Installation
```bash
# Install PHPUnit via Composer (if not already installed)
composer require --dev phpunit/phpunit ^9.5

# Or install globally
composer global require phpunit/phpunit ^9.5
```

### Running All Tests
```bash
# From the project root directory
phpunit --configuration test/phpunit.xml

# Or from the test directory
cd test
phpunit
```

### Running Specific Test Suites
```bash
# Run only unit tests
phpunit --configuration test/phpunit.xml --testsuite "Unit Tests"

# Run only integration tests
phpunit --configuration test/phpunit.xml --testsuite "Integration Tests"
```

### Running Individual Test Files
```bash
# Run a specific test file
phpunit test/Unit/Core/DateHelperTest.php

# Run with verbose output
phpunit --verbose test/Unit/Database/DatabaseTest.php
```

### Code Coverage
```bash
# Generate code coverage report (requires Xdebug)
phpunit --configuration test/phpunit.xml --coverage-html coverage/
```

## Test Implementation Guidelines

### Unit Test Best Practices
1. **Isolation**: Each test should be independent and not rely on other tests
2. **Mocking**: Use mocks for external dependencies (database, APIs, file system)
3. **Naming**: Use descriptive test method names that explain what is being tested
4. **Assertions**: Use specific assertions and include meaningful failure messages
5. **Setup/Teardown**: Use setUp() and tearDown() methods for test preparation and cleanup

### Test Naming Convention
- Test classes should end with `Test` (e.g., `DatabaseTest`)
- Test methods should start with `test` and describe the scenario (e.g., `testGetDateWithValidInput`)
- Use camelCase for test method names

### Mock Usage
- Mock external dependencies to ensure tests are fast and reliable
- Use PHPUnit's built-in mocking framework
- Verify mock interactions when testing behavior

## Classes Requiring Additional Tests

### High Priority
1. **TicketTrait** - Core business logic for ticket management (952 lines)
2. **Controllers** - All controller classes for API endpoints and business logic
3. **Core Classes** - Additional helper classes like MailHelper, TicketHelper, etc.
4. **Authentication** - GraphCertificateAuthenticator and related auth classes

### Medium Priority
1. **Struct Classes** - Data transfer objects and value objects
2. **Reporting Classes** - Report generation and data analysis
3. **Application Classes** - Application-level logic and configuration

### Integration Tests Needed
1. **Database Operations** - Real database interactions with test data
2. **Microsoft Graph API** - Integration with Exchange Online
3. **Email Processing** - Mail parsing and processing workflows
4. **Authentication Flow** - Complete authentication and authorization flow

## Configuration

### Test Database
For integration tests, configure a separate test database:
```php
// In bootstrap.php or test configuration
$testConfig = [
    'db_host' => 'localhost',
    'db_name' => 'helpdesk_test',
    'db_user' => 'test_user',
    'db_pass' => 'test_pass'
];
```

### Environment Variables
Set the `TESTING` environment variable to enable test mode:
```php
define('TESTING', true);
```

## Continuous Integration

### GitHub Actions Example
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
          extensions: pdo, pdo_mysql
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: phpunit --configuration test/phpunit.xml
```

## Contributing

When adding new tests:
1. Follow the existing directory structure
2. Use appropriate test categories (Unit vs Integration)
3. Include both positive and negative test cases
4. Test edge cases and error conditions
5. Update this README if adding new test categories or significant changes

## Notes

- The current test suite focuses on core functionality and helper classes
- Some classes with heavy database dependencies may require integration tests rather than unit tests
- Mock objects are used extensively to avoid external dependencies in unit tests
- Performance tests are included for critical operations like GUID generation