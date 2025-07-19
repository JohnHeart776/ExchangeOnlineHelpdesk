# Exchange Online Helpdesk - Development Guidelines

## Build/Configuration Instructions

### Environment Setup
This project uses a custom PHP framework without Composer dependency management. The application requires:

- **PHP 8.4+** (uses nullable type declarations and other modern PHP features)
- **MySQL/MariaDB** with utf8mb4 charset support
- **Web server** (Apache/Nginx) with PHP support

### Database Configuration
The application uses environment variables for database configuration:

```bash
DBHOST=localhost
DBUSER=your_db_user
DBPASSWORD=your_db_password
DBNAME=your_db_name
```

The database class supports both MySQLi and PDO connections simultaneously and uses utf8mb4 charset for full Unicode support.

### Application Bootstrap
The application uses a custom autoloading system through `src/bootstrap.php`:
- Manual includes via `_import.php` files in each directory
- Custom error and exception handlers with email notifications
- Smarty templating engine initialization
- Session management through Login class

### Vendor Libraries
The project includes vendor libraries manually in `src/Vendor/`:
- **Kint** (debugging tool) - `kint.phar`
- **PHPMailer** (email functionality)
- **Smarty 4.3.0** (templating engine)

### Directory Structure
```
src/
├── Application/     # Application-specific classes
├── Auth/           # Authentication and authorization
├── Client/         # Client-related functionality
├── Controller/     # MVC controllers
├── Core/           # Core utility classes
├── Database/       # Database abstraction layer
├── Reporting/      # Reporting functionality
├── Struct/         # Data structures
├── Trait/          # PHP traits
└── Vendor/         # Third-party libraries
```

## Testing Information

### PHPUnit Configuration
The project uses **PHPUnit 9.5** with comprehensive testing configuration:

- **Bootstrap file**: `test/bootstrap.php`
- **Test suites**: Unit Tests and Integration Tests
- **Code coverage**: Includes `src/` directory (excludes Vendor and bootstrap.php)
- **Environment**: `APP_ENV=testing`

### Test Structure
```
test/
├── Unit/           # Unit tests
│   ├── Client/
│   ├── Core/
│   └── Database/
├── Integration/    # Integration tests
├── bootstrap.php   # Test initialization
└── phpunit.xml     # PHPUnit configuration
```

### Running Tests
**Note**: PHPUnit must be installed separately as the project doesn't use Composer.

```bash
# Install PHPUnit globally or use PHAR
# From project root:
cd test
phpunit --testdox                    # Run all tests with documentation format
phpunit Unit/Core/DateHelperTest.php # Run specific test
phpunit --coverage-html coverage/   # Generate coverage report
```

### Test Bootstrap Setup
The test bootstrap (`test/bootstrap.php`) provides:
- Custom autoloader for test and source classes
- Test database configuration
- UTC timezone setting for consistent testing
- Integration with main application bootstrap

### Writing Tests
Follow these patterns when creating tests:

1. **Namespace**: Use `Test\Unit\[Directory]` or `Test\Integration\[Directory]`
2. **File naming**: `[ClassName]Test.php`
3. **Class naming**: `[ClassName]Test extends TestCase`
4. **Method naming**: Descriptive names like `testGetDateWithValidDateStringReturnsCorrectDateTime`

### Example Test Structure
```php
<?php

namespace Test\Unit\Core;

use PHPUnit\Framework\TestCase;
use YourClass;

class YourClassTest extends TestCase
{
    public function testMethodReturnsExpectedResult(): void
    {
        $result = YourClass::method();
        
        $this->assertInstanceOf(ExpectedClass::class, $result);
        $this->assertEquals('expected', $result->getValue());
    }
}
```

## Additional Development Information

### Code Style and Conventions

#### File Naming
- **Classes**: `ClassName.class.php`
- **Tests**: `ClassNameTest.php`
- **Imports**: `_import.php` (for directory-level includes)

#### Class Structure
- Use **static methods** for utility classes (DateHelper, GuidHelper)
- Implement **singleton pattern** for service classes (Database)
- Use **namespaces** for organization (`Database\Database`, `Test\Unit\Core`)

#### Documentation
- Use **PHPDoc comments** with proper type hints
- Include `@param`, `@return`, and `@throws` annotations
- Document complex business logic and edge cases

#### Error Handling
- Custom exception hierarchy for different error types
- Comprehensive error logging and email notifications
- Proper exception chaining and context preservation

### Database Patterns
- **Dual connection support**: Both MySQLi and PDO available
- **Exception handling**: Custom DatabaseException hierarchy
- **Charset**: Always use utf8mb4 for full Unicode support
- **Singleton pattern**: Database instance management

### Testing Best Practices
- **Comprehensive coverage**: Test positive cases, edge cases, and exceptions
- **Performance testing**: Include performance assertions where relevant
- **Method validation**: Test method existence and accessibility
- **Consistent assertions**: Use appropriate PHPUnit assertion methods

### Security Considerations
- Environment variables for sensitive configuration
- Proper charset handling (utf8mb4)
- Custom error handlers prevent information disclosure
- Session management through dedicated Login class

### Debugging Tools
- **Kint**: Available for advanced debugging (`d()`, `dd()` functions)
- **Custom error handlers**: Detailed error reporting with stack traces
- **Test environment**: Separate configuration for testing

### Performance Notes
- Manual autoloading system (no Composer overhead)
- Smarty template compilation caching
- Database connection reuse through singleton pattern
- Efficient error handling with conditional email notifications