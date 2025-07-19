# Contributing to Exchange Online Helpdesk (EOTS)

Welcome to the Exchange Online Helpdesk project! We're excited that you're interested in contributing to this comprehensive ticket management system. This guide will help you understand how to contribute effectively to the project.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Development Environment Setup](#development-environment-setup)
3. [Areas Where Contributions Are Needed](#areas-where-contributions-are-needed)
4. [Code Contribution Guidelines](#code-contribution-guidelines)
5. [Testing Requirements](#testing-requirements)
6. [Documentation Standards](#documentation-standards)
7. [Pull Request Process](#pull-request-process)
8. [Code Style and Conventions](#code-style-and-conventions)
9. [Security Guidelines](#security-guidelines)
10. [Getting Help](#getting-help)

## Project Overview

EOTS is an enterprise-grade ticket management system that integrates with Microsoft 365 environments. It automatically processes emails into tickets, provides role-based access control, and includes AI-powered features for automated responses and categorization.

### Key Technologies
- **Backend**: PHP 8.4+ with custom framework (no Composer)
- **Database**: MySQL/MariaDB with utf8mb4 charset
- **Templating**: Smarty 4.3.0
- **Testing**: PHPUnit 9.5
- **Authentication**: Microsoft Azure AD OAuth2
- **APIs**: Microsoft Graph API, OpenAI, Azure AI

### Architecture
The project follows a custom MVC-like architecture with:
- `src/` - Core application code
- `test/` - PHPUnit tests (Unit and Integration)
- `docs/` - Comprehensive documentation
- `assets/` - Frontend resources
- `api/` - RESTful API endpoints

## Development Environment Setup

### Prerequisites
- **PHP 8.4+** with extensions: mysqli, pdo, curl, json, mbstring
- **MySQL/MariaDB** with utf8mb4 charset support
- **Web server** (Apache/Nginx) with PHP support
- **PHPUnit 9.5** (install separately - project doesn't use Composer)

### Environment Configuration
Create environment variables for database configuration:
```bash
DBHOST=localhost
DBUSER=your_db_user
DBPASSWORD=your_db_password
DBNAME=your_db_name
APP_ENV=development  # or testing for tests
```

### Local Setup
1. Clone the repository
2. Set up your web server to serve from the project root
3. Configure database connection via environment variables
4. Import database schema from `_install/schema/`
5. Install PHPUnit separately for testing

### Testing Setup
```bash
cd test
phpunit --testdox                    # Run all tests
phpunit Unit/Core/DateHelperTest.php # Run specific test
phpunit --coverage-html coverage/   # Generate coverage report
```

## Areas Where Contributions Are Needed

We have identified several critical areas where contributions would be highly valuable:

### 🔴 HIGH PRIORITY - Critical Issues

#### 1. Missing Test Coverage
**Impact**: High risk of regressions, difficult to refactor safely

**Specific Needs:**
- **Core Classes** (30+ classes without tests):
  - `TicketTextCleaner.class.php` - Complex HTML processing (339 lines)
  - `AzureAiClient.class.php` - AI integration with caching (198 lines)
  - `OpenAiClient.class.php` - Alternative AI client
  - `CurlHelper.class.php` - HTTP request utility
  - `DayNameHelper.class.php` - Internationalization utility
  - All other classes in `src/Core/` directory

- **Utility Functions** (`src/functions.php`):
  - 694 lines of utility functions with NO test coverage
  - Critical functions: `base32_encode()`, `base32_decode()`, `guid()`, `reorderDays()`
  - Security functions: `clean_input()`, `getClientIP()`
  - Formatting utilities: `formatNumber()`, `slugify()`, `file_extension()`

**How to Contribute:**
- Create comprehensive unit tests following existing patterns in `test/Unit/`
- Aim for 100% code coverage
- Test positive cases, edge cases, and error conditions
- Include performance tests where relevant

#### 2. Security Vulnerabilities
**Impact**: Potential security breaches and data compromise

**Specific Issues:**
- **JWT Token Handling** (`Login.class.php`):
  - JWT parsing without proper validation (lines 42-55, 99-101)
  - Need to implement proper JWT library with signature verification

- **GUID Generation** (`GuidHelper.class.php`):
  - Uses `mt_rand()` instead of cryptographically secure `random_int()`
  - Predictable GUIDs pose security risks

- **Input Sanitization** (`functions.php`):
  - `clean_input()` function uses basic sanitization
  - Need enhanced sanitization with proper validation

**How to Contribute:**
- Implement secure JWT handling with proper libraries
- Replace insecure random functions with cryptographically secure alternatives
- Enhance input validation and sanitization functions
- Add security-focused unit tests

#### 3. Code Duplication
**Impact**: Maintenance burden, inconsistency risk

**Specific Issues:**
- **Day Name Functions** (4 different implementations):
  - `weekDayNameGerman()`, `setDayNames()`, `getDateDayName()`, `getEnglishDayName()`
  - Need consolidation into single `DayNameHelper` class

- **JWT Parsing Logic** (`Login.class.php`):
  - Duplicated code in lines 42-55 and 99-101
  - Extract to private method

- **AI Client Methods** (`AzureAiClient.class.php`):
  - `getResponse()` and `getResponseForMessageArray()` are nearly identical
  - Consolidate into single method with parameters

**How to Contribute:**
- Create unified helper classes to replace duplicated code
- Refactor existing code to use new consolidated methods
- Ensure backward compatibility during refactoring
- Add comprehensive tests for new consolidated code

### 🟡 MEDIUM PRIORITY - Code Quality Issues

#### 4. Large, Complex Methods
**Specific Issues:**
- `TicketTextCleaner.cleanOld()` - 235 lines doing too much
- `Login.loginUserFromGraphResponse()` - 58 lines with mixed responsibilities

**How to Contribute:**
- Break large methods into smaller, focused methods
- Apply Single Responsibility Principle
- Maintain existing functionality while improving structure

#### 5. Inconsistent Error Handling
**Issues:**
- Mixed exception types across classes
- Inconsistent error messages (mixed German/English)
- Need for specific exception hierarchy

**How to Contribute:**
- Create comprehensive exception hierarchy
- Standardize error handling patterns
- Implement proper exception chaining and context

#### 6. Missing Documentation
**Issues:**
- Inconsistent PHPDoc comments across classes
- Many methods lack proper documentation
- Missing parameter descriptions and examples

**How to Contribute:**
- Add comprehensive PHPDoc for all public methods
- Include parameter descriptions, return types, and examples
- Document complex business logic and edge cases

### 🟢 LOW PRIORITY - Enhancement Opportunities

#### 7. Performance Optimizations
- Remove deprecated functions like `getLatestGitHeadLegacyFOpen()`
- Optimize database queries and caching mechanisms
- Improve AI client response handling

#### 8. Internationalization
- Standardize language usage (currently mixed German/English)
- Implement proper i18n framework
- Add support for additional languages

## Code Contribution Guidelines

### Before You Start
1. **Check existing issues** - Look for related GitHub issues
2. **Review documentation** - Read relevant files in `docs/` directory
3. **Understand the architecture** - Review `README.md` and process definitions
4. **Set up development environment** - Follow setup instructions above

### Making Changes
1. **Create feature branch** from `main`
2. **Write tests first** (TDD approach recommended)
3. **Implement changes** following coding standards
4. **Run all tests** to ensure no regressions
5. **Update documentation** if needed

### Commit Guidelines
- Use clear, descriptive commit messages
- Reference issue numbers when applicable
- Keep commits focused and atomic
- Follow conventional commit format when possible

## Testing Requirements

### Test Coverage Expectations
- **New code**: 100% test coverage required
- **Bug fixes**: Must include regression tests
- **Refactoring**: Maintain or improve existing coverage

### Test Types
1. **Unit Tests** (`test/Unit/`):
   - Test individual classes and methods
   - Mock external dependencies
   - Focus on business logic

2. **Integration Tests** (`test/Integration/`):
   - Test component interactions
   - Database integration tests
   - API endpoint tests

### Test Naming Conventions
- **Files**: `ClassNameTest.php`
- **Classes**: `ClassNameTest extends TestCase`
- **Methods**: Descriptive names like `testGetDateWithValidDateStringReturnsCorrectDateTime`

### Running Tests
```bash
cd test
phpunit --testdox                    # All tests with documentation
phpunit Unit/Core/                   # Specific directory
phpunit --coverage-html coverage/   # Coverage report
```

## Documentation Standards

### PHPDoc Requirements
All public methods must include:
```php
/**
 * Brief description of what the method does
 *
 * @param string $parameter Description of parameter
 * @param int|null $optional Optional parameter description
 * @return bool Description of return value
 * @throws CustomException When this exception is thrown
 */
public function methodName(string $parameter, ?int $optional = null): bool
{
    // Implementation
}
```

### Code Comments
- Use clear, concise comments for complex logic
- Explain "why" not "what"
- Keep comments up-to-date with code changes
- Use English for all comments and documentation

### Documentation Files
- Update relevant files in `docs/` directory
- Follow existing documentation structure
- Include examples and use cases
- Keep process definitions updated

## Pull Request Process

### Before Submitting
1. **Ensure all tests pass** locally
2. **Run code coverage** and maintain/improve coverage
3. **Update documentation** as needed
4. **Self-review your changes**
5. **Rebase on latest main** if needed

### PR Description Template
```markdown
## Description
Brief description of changes made

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Testing
- [ ] Unit tests added/updated
- [ ] Integration tests added/updated
- [ ] All tests pass locally
- [ ] Code coverage maintained/improved

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No new security vulnerabilities introduced
```

### Review Process
1. **Automated checks** must pass (tests, coverage)
2. **Code review** by maintainers
3. **Security review** for security-related changes
4. **Documentation review** for significant changes

## Code Style and Conventions

### File Naming
- **Classes**: `ClassName.class.php`
- **Tests**: `ClassNameTest.php`
- **Imports**: `_import.php` (for directory-level includes)

### Coding Standards
- **PHP Version**: Target PHP 8.4+ features
- **Naming**: Use camelCase for methods and variables
- **Classes**: Use PascalCase for class names
- **Constants**: Use UPPER_SNAKE_CASE
- **Type Hints**: Always use parameter and return type declarations

### Class Structure
```php
<?php

namespace Appropriate\Namespace;

/**
 * Class description
 */
class ClassName
{
    private string $property;

    /**
     * Constructor description
     */
    public function __construct(string $parameter)
    {
        $this->property = $parameter;
    }

    /**
     * Method description
     */
    public function methodName(string $input): string
    {
        return $this->processInput($input);
    }

    /**
     * Private helper method
     */
    private function processInput(string $input): string
    {
        // Implementation
        return $processed;
    }
}
```

### Database Patterns
- Use **utf8mb4** charset for all database operations
- Implement proper **exception handling** with custom exceptions
- Follow **singleton pattern** for service classes
- Use **prepared statements** for all queries

## Security Guidelines

### Security Best Practices
1. **Input Validation**: Validate all user inputs
2. **Output Encoding**: Properly encode outputs
3. **Authentication**: Use secure authentication methods
4. **Authorization**: Implement proper access controls
5. **Cryptography**: Use secure random functions and proper encryption

### Security Review Requirements
- All security-related changes require thorough review
- Use cryptographically secure random functions
- Implement proper JWT validation
- Follow OWASP security guidelines

### Reporting Security Issues
- **DO NOT** create public issues for security vulnerabilities
- Contact maintainers privately
- Provide detailed reproduction steps
- Allow time for fix before disclosure

## Getting Help

### Resources
- **Documentation**: Check `docs/` directory for detailed information
- **Code Examples**: Review existing tests and implementations
- **Architecture**: See `README.md` for system overview

### Communication
- **GitHub Issues**: For bugs, feature requests, and questions
- **Pull Request Comments**: For code-specific discussions
- **Documentation**: Refer to process definitions in `docs/`

### Common Questions

**Q: How do I run tests?**
A: Install PHPUnit separately, then run `phpunit --testdox` from the `test/` directory.

**Q: What's the priority for contributions?**
A: Focus on HIGH PRIORITY items first: missing tests, security issues, and code duplication.

**Q: How do I handle database connections in tests?**
A: Use the test bootstrap configuration with `APP_ENV=testing` environment variable.

**Q: What coding standards should I follow?**
A: Follow PSR-1 basic coding standard with camelCase naming and comprehensive type hints.

## Thank You!

Thank you for contributing to the Exchange Online Helpdesk project! Your contributions help make this system more robust, secure, and maintainable for everyone. Every contribution, whether it's fixing a bug, adding tests, improving documentation, or implementing new features, is valuable and appreciated.

Remember: **Quality over quantity**. We prefer well-tested, well-documented, secure code over quick fixes. Take your time to understand the codebase and follow the guidelines outlined in this document.

Happy coding! 🚀