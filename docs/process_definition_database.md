# Database Class Process Definition

## Overview
The `Database` class is the core data access layer for the Exchange Online Helpdesk application. It implements a singleton pattern and provides dual database connectivity (MySQLi and PDO), comprehensive query execution, error handling, and data manipulation capabilities with robust exception management.

## Class Location
- **File**: `src/Database/Database.class.php`
- **Namespace**: `Database`
- **Type**: Singleton service class
- **Dependencies**: PDO, mysqli, custom exception hierarchy

## Exception Hierarchy

### Custom Exception Classes
- **DatabaseException**: Base exception for all database-related errors
- **DatabaseConnectException**: Connection establishment failures
- **DatabaseCharsetException**: Character set configuration errors
- **DatabaseQueryException**: Query execution failures with detailed context

## Core Processes

### 1. Singleton Instance Management
**Method**: `getInstance()`
- **Purpose**: Ensures single database instance throughout application lifecycle
- **Process Flow**:
  1. Checks if instance already exists
  2. Creates new instance if needed
  3. Returns existing or new Database instance
- **Input**: None
- **Output**: Database singleton instance
- **Pattern**: Thread-safe singleton implementation

### 2. Database Connection Management
**Method**: `__construct()`
- **Purpose**: Initializes dual database connections (MySQLi and PDO)
- **Process Flow**:
  1. Establishes MySQLi connection with environment variables
  2. Sets UTF8MB4 charset for full Unicode support
  3. Initializes PDO connection separately
  4. Handles connection failures with specific exceptions
- **Environment Variables**: DBHOST, DBUSER, DBPASSWORD, DBNAME
- **Charset**: utf8mb4 for full Unicode support
- **Error Handling**: Throws DatabaseConnectException on failure

**Method**: `getLink()`
- **Purpose**: Provides access to MySQLi connection object
- **Process Flow**:
  1. Returns active MySQLi connection
  2. Used for MySQLi-specific operations
- **Input**: None
- **Output**: mysqli object or null

### 3. PDO Connection Management
**Method**: `initializePDOConnection()`
- **Purpose**: Establishes PDO database connection with proper configuration
- **Process Flow**:
  1. Creates PDO instance with DSN from environment variables
  2. Sets PDO attributes for error handling and fetch modes
  3. Configures UTF8MB4 charset
  4. Handles PDO-specific connection errors
- **Configuration**: Error mode, fetch mode, charset settings
- **Error Handling**: Throws DatabaseConnectException on PDO failures

**Method**: `getPDOConnection()`
- **Purpose**: Provides access to PDO connection object
- **Process Flow**:
  1. Returns active PDO connection
  2. Used for prepared statement operations
- **Input**: None
- **Output**: PDO object or null

### 4. Connection Lifecycle Management
**Method**: `close()`
- **Purpose**: Properly closes MySQLi database connection
- **Process Flow**:
  1. Closes active MySQLi connection
  2. Cleans up connection resources
- **Input**: None
- **Output**: Void

**Method**: `closePDO()`
- **Purpose**: Properly closes PDO database connection
- **Process Flow**:
  1. Sets PDO connection to null
  2. Triggers PDO cleanup processes
- **Input**: None
- **Output**: Void

### 5. MySQLi Query Operations
**Method**: `query()`
- **Purpose**: Executes raw SQL queries using MySQLi
- **Process Flow**:
  1. Executes query on MySQLi connection
  2. Returns query result or boolean
  3. Handles query execution errors
- **Input**: SQL query string
- **Output**: Mixed (result set or boolean)
- **Error Handling**: Basic error reporting

**Method**: `get()`
- **Purpose**: Executes SELECT queries and returns formatted results
- **Process Flow**:
  1. Executes query using MySQLi
  2. Fetches all results as associative arrays
  3. Returns single record or array of records
  4. Handles empty result sets
- **Input**: SQL query, single record flag
- **Output**: Array of results or single record
- **Flexibility**: Can return single record or multiple records

### 6. PDO Query Operations
**Method**: `executePDOStatement()`
- **Purpose**: Executes prepared statements with parameter binding
- **Process Flow**:
  1. Prepares SQL statement using PDO
  2. Binds parameters securely
  3. Executes prepared statement
  4. Returns PDOStatement object
  5. Comprehensive error handling with context
- **Input**: SQL query, parameters array
- **Output**: PDOStatement object
- **Security**: Prevents SQL injection through parameter binding

**Method**: `queryPDO()`
- **Purpose**: Simplified interface for PDO prepared statements
- **Process Flow**:
  1. Delegates to executePDOStatement()
  2. Provides cleaner API for common operations
- **Input**: SQL query, parameters array
- **Output**: PDOStatement object

**Method**: `getPDO()`
- **Purpose**: Executes PDO queries and returns formatted results
- **Process Flow**:
  1. Executes prepared statement with parameters
  2. Fetches results as associative arrays
  3. Returns single record or multiple records
  4. Handles empty result sets gracefully
- **Input**: SQL query, parameters, single record flag
- **Output**: Array of results or single record

**Method**: `fetchOnePDO()`
- **Purpose**: Convenience method for fetching single records via PDO
- **Process Flow**:
  1. Calls getPDO() with single record flag
  2. Returns only first matching record
- **Input**: SQL query, parameters array
- **Output**: Single record array or null

### 7. Data Security and Sanitization
**Method**: `filter()`
- **Purpose**: Sanitizes input data for MySQLi queries
- **Process Flow**:
  1. Uses MySQLi real_escape_string() for sanitization
  2. Prevents basic SQL injection attacks
  3. Returns sanitized string
- **Input**: Raw string data
- **Output**: Sanitized string
- **Note**: Less secure than prepared statements

**Method**: `filterPDO()`
- **Purpose**: Provides PDO-compatible data filtering
- **Process Flow**:
  1. Uses PDO quote() method for string escaping
  2. Returns properly quoted string for PDO
- **Input**: Raw string data
- **Output**: Quoted string for PDO
- **Recommendation**: Use prepared statements instead

### 8. Record Counting Operations
**Method**: `count()`
- **Purpose**: Executes COUNT queries using MySQLi
- **Process Flow**:
  1. Executes provided COUNT query
  2. Extracts count value from result
  3. Returns integer count
- **Input**: COUNT SQL query
- **Output**: Integer count value

**Method**: `countPDO()`
- **Purpose**: Executes COUNT queries using PDO with parameters
- **Process Flow**:
  1. Prepares and executes COUNT query with parameters
  2. Fetches count result securely
  3. Returns integer count with error handling
- **Input**: COUNT SQL query
- **Output**: Integer count value
- **Security**: Uses prepared statements for safety

### 9. Insert ID Retrieval
**Method**: `lastInsertIdFromMysqli()`
- **Purpose**: Retrieves last inserted record ID from MySQLi
- **Process Flow**:
  1. Gets insert_id from MySQLi connection
  2. Returns last auto-increment value
- **Input**: None
- **Output**: String/integer ID or null

**Method**: `lastInsertIdFromPdo()`
- **Purpose**: Retrieves last inserted record ID from PDO
- **Process Flow**:
  1. Uses PDO lastInsertId() method
  2. Returns last auto-increment value
- **Input**: None
- **Output**: String/integer ID or null

## Integration Points

### Environment Configuration
- **Database Credentials**: Uses environment variables for connection details
- **Charset Configuration**: Enforces UTF8MB4 for full Unicode support
- **Connection Parameters**: Configurable through environment settings

### Exception Integration
- **Custom Exception Hierarchy**: Provides detailed error context
- **Error Propagation**: Maintains error chains for debugging
- **Query Context**: Includes failed queries in exception details

### Application Integration
- **Global Access**: Available as global `$d` variable
- **Singleton Pattern**: Ensures consistent database state
- **Dual API Support**: Supports both MySQLi and PDO paradigms

## Security Features

### SQL Injection Prevention
- **Prepared Statements**: Primary defense through PDO prepared statements
- **Parameter Binding**: Secure parameter handling
- **Input Sanitization**: Fallback sanitization methods

### Connection Security
- **Secure Credentials**: Environment-based credential management
- **Connection Encryption**: Supports SSL/TLS connections
- **Charset Security**: UTF8MB4 prevents charset-based attacks

### Error Information Security
- **Controlled Error Exposure**: Detailed errors for development, limited for production
- **Query Context**: Includes query information for debugging
- **Exception Chaining**: Maintains error context without exposure

## Performance Considerations

### Connection Management
- **Singleton Pattern**: Reuses database connections
- **Connection Pooling**: Leverages database connection pooling
- **Resource Cleanup**: Proper connection closure

### Query Optimization
- **Prepared Statement Caching**: PDO caches prepared statements
- **Result Set Management**: Efficient memory usage for large results
- **Query Execution**: Optimized query execution paths

### Memory Management
- **Result Set Handling**: Proper cleanup of result sets
- **Connection Resources**: Managed connection lifecycle
- **Exception Handling**: Minimal memory overhead for errors

## Usage Patterns

### 1. Basic Query Execution
```php
$database = Database::getInstance();
$results = $database->get("SELECT * FROM table WHERE condition");
```

### 2. Secure Parameterized Queries
```php
$database = Database::getInstance();
$results = $database->getPDO("SELECT * FROM table WHERE id = ?", [$id]);
```

### 3. Record Counting
```php
$database = Database::getInstance();
$count = $database->countPDO("SELECT COUNT(*) FROM table WHERE status = ?", ['active']);
```

## Error Handling Strategy
- **Comprehensive Exception Hierarchy**: Different exception types for different error categories
- **Query Context Preservation**: Failed queries included in exceptions
- **Error Chain Maintenance**: Original exceptions preserved in error chain
- **Development vs Production**: Configurable error detail levels