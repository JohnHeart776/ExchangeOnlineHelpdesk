# TicketList Class Process Definition

## Overview
The `TicketList` class provides static methods for retrieving collections of tickets based on various criteria. It serves as a query interface for ticket collections, handling different filtering and sorting requirements for ticket management workflows.

## Class Location
- **File**: `src/Core/TicketList.class.php`
- **Type**: Static utility class
- **Dependencies**: Database (global $d), Ticket class, Status entity

## Core Processes

### 1. Latest Open Tickets Retrieval
**Method**: `getLatestOpenTickets()`
- **Purpose**: Retrieves the most recent open tickets with configurable limits
- **Process Flow**:
  1. Validates and sets limit parameter (default: no limit, max: 10,000)
  2. Executes SQL query joining Ticket and Status tables
  3. Filters for open tickets only (Status.IsOpen = 1)
  4. Orders by creation date descending (newest first)
  5. Applies limit to result set
  6. Instantiates Ticket objects for each result
  7. Returns array of Ticket objects
- **Input**: Optional integer limit (nullable, max 10,000)
- **Output**: Array of Ticket objects
- **SQL Query**:
  ```sql
  SELECT t.TicketId 
  FROM Ticket t 
  LEFT JOIN Status s ON t.StatusId = s.StatusId 
  WHERE (s.IsOpen = 1)
  ORDER BY t.CreatedDatetime DESC 
  LIMIT [limit]
  ```

### 2. All Tickets Retrieval
**Method**: `getAllTickets()`
- **Purpose**: Retrieves all tickets in the system regardless of status
- **Process Flow**:
  1. Executes simple SQL query on Ticket table
  2. Orders by TicketId descending (newest first by ID)
  3. Instantiates Ticket objects for each result
  4. Returns complete array of all tickets
- **Input**: None
- **Output**: Array of all Ticket objects
- **SQL Query**:
  ```sql
  SELECT TicketId FROM Ticket ORDER BY TicketId DESC
  ```
- **Exception Handling**: Throws DatabaseQueryException on query failure

### 3. Due Tickets Retrieval
**Method**: `getDueTickets()`
- **Purpose**: Retrieves tickets that are past their due date and still active
- **Process Flow**:
  1. Executes SQL query joining Ticket and Status tables
  2. Filters for tickets past due date (DueDatetime < NOW())
  3. Excludes tickets in final status (IsFinal IS NULL OR IsFinal = 0)
  4. Orders by due date ascending (most overdue first)
  5. Instantiates Ticket objects for each result
  6. Returns array of overdue tickets
- **Input**: None
- **Output**: Array of overdue Ticket objects
- **SQL Query**:
  ```sql
  SELECT TicketId 
  FROM Ticket t
  LEFT JOIN Status s ON t.StatusId = s.StatusId
  WHERE t.DueDatetime < NOW()
  AND (s.IsFinal IS NULL OR s.IsFinal = 0)
  ORDER BY t.DueDatetime ASC
  ```
- **Exception Handling**: Throws DatabaseQueryException on query failure

## Integration Points

### Database Integration
- **Global Database Object**: Uses global `$d` for database operations
- **Query Execution**: Utilizes database `get()` method for result retrieval
- **Error Handling**: Leverages database exception system

### Status System Integration
- **Open Status Filtering**: Integrates with Status.IsOpen flag
- **Final Status Exclusion**: Respects Status.IsFinal flag for active ticket identification
- **Status Joins**: Uses LEFT JOIN to handle tickets without status

### Ticket Object Integration
- **Lazy Loading**: Creates Ticket objects with ID-based instantiation
- **Object Hydration**: Ticket constructor handles data loading from database

## Performance Considerations

### Query Optimization
- **Indexed Queries**: Relies on proper indexing of TicketId, StatusId, CreatedDatetime, DueDatetime
- **Efficient Joins**: Uses LEFT JOIN for optional status relationships
- **Limit Controls**: Enforces maximum limit of 10,000 records to prevent memory issues

### Memory Management
- **Lazy Object Creation**: Only creates objects for returned results
- **Controlled Result Sets**: Limit enforcement prevents excessive memory usage
- **Efficient Iteration**: Uses simple foreach loops for object instantiation

## Usage Patterns

### 1. Dashboard Display
- **Latest Open Tickets**: Primary use case for dashboard ticket lists
- **Configurable Limits**: Allows different page sizes and display requirements

### 2. Administrative Overview
- **All Tickets**: Used for administrative reporting and bulk operations
- **Complete Dataset**: Provides access to entire ticket collection

### 3. SLA Monitoring
- **Due Tickets**: Critical for SLA compliance monitoring
- **Priority Ordering**: Most overdue tickets appear first for urgent attention

## Security Considerations
- **SQL Injection Prevention**: Uses parameterized queries through database layer
- **Access Control**: Relies on application-level authentication (no built-in access control)
- **Data Exposure**: Returns complete ticket objects (application must handle sensitive data)

## Error Handling
- **Database Exceptions**: Propagates DatabaseQueryException for query failures
- **Null Safety**: Handles empty result sets gracefully
- **Limit Validation**: Enforces reasonable limits to prevent system overload

## Scalability Notes
- **Large Dataset Handling**: 10,000 record limit prevents memory exhaustion
- **Query Performance**: Dependent on proper database indexing
- **Object Instantiation**: May become bottleneck with large result sets
- **Pagination Consideration**: No built-in pagination support (handled at application level)