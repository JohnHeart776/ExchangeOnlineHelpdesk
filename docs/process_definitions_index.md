# Process Definitions Index

## Overview
This directory contains detailed process definitions for the core classes in the Exchange Online Helpdesk application. Each document explains the processes, methods, integration points, and usage patterns for individual classes.

## Available Process Definitions

### Ticket Management Classes

#### 1. TicketHelper Class
**File**: `process_definition_ticket_helper.md`
- **Purpose**: Core ticket creation and string manipulation utilities
- **Key Processes**:
  - Creating tickets from Microsoft Graph emails
  - Initializing empty tickets with default values
  - Ticket marker detection and manipulation in email subjects
  - String processing for ticket tracking
- **Integration**: Works with TicketController, TicketTooling, SlaHelper, TicketNumberHelper

#### 2. TicketTooling Class
**File**: `process_definition_ticket_tooling.md`
- **Purpose**: Ticket security and data conversion utilities
- **Key Processes**:
  - Generating cryptographically secure ticket secrets
  - Converting Microsoft Graph emails to ticket comments
  - Data sanitization and JSON serialization
- **Security Features**: Multi-entropy secret generation, SHA1 hashing
- **Integration**: Works with TicketTextCleaner, GraphMail structures

#### 3. TicketList Class
**File**: `process_definition_ticket_list.md`
- **Purpose**: Ticket collection querying and retrieval
- **Key Processes**:
  - Retrieving latest open tickets with configurable limits
  - Getting all tickets for administrative purposes
  - Finding overdue tickets for SLA monitoring
- **Performance**: Enforces 10,000 record limits, optimized queries
- **Integration**: Database queries with Status system integration

### Authentication and Security Classes

#### 4. Login Class
**File**: `process_definition_login.md`
- **Purpose**: Core authentication and session management
- **Key Processes**:
  - User session lifecycle management
  - Microsoft Graph token management and refresh
  - Role-based access control (Guest, User, Agent, Admin)
  - Navigation flow control and redirects
- **Security**: Secure session handling, automatic token refresh
- **Integration**: GraphClient, User management, session storage

#### 5. GraphCertificateAuthenticator Class
**File**: `process_definition_graph_certificate_authenticator.md`
- **Purpose**: Certificate-based authentication for Microsoft Graph API
- **Key Processes**:
  - X.509 certificate validation and management
  - JWT assertion creation with RS256 signing
  - OAuth2 client credentials flow implementation
  - Azure AD organization information retrieval
- **Security**: Certificate-key pair validation, secure JWT creation
- **Compliance**: RFC 7515/7519, OAuth 2.0, Azure AD standards

### API Integration Classes

#### 6. GraphClient Class
**File**: `process_definition_graph_client.md`
- **Purpose**: Microsoft Graph API communication interface
- **Key Processes**:
  - HTTP request management (GET, POST, PATCH)
  - User information and profile image retrieval
  - Email operations (sending, fetching, attachments)
  - Email subject manipulation for ticket tracking
- **Features**: Multipart email support, attachment handling, pagination
- **Integration**: GraphCertificateAuthenticator, CurlHelper, data structures

### Data Access Classes

#### 7. Database Class
**File**: `process_definition_database.md`
- **Purpose**: Core data access layer with dual database support
- **Key Processes**:
  - Singleton database connection management
  - Dual connectivity (MySQLi and PDO)
  - Secure query execution with prepared statements
  - Comprehensive exception handling
- **Security**: SQL injection prevention, input sanitization, secure connections
- **Features**: UTF8MB4 charset support, connection pooling, error context preservation

## Class Relationships and Dependencies

### Core System Flow
```
Email Processing Flow:
GraphClient → TicketHelper → TicketTooling → Database
     ↓              ↓            ↓
GraphMail → Ticket Creation → TicketComment → Data Persistence
```

### Authentication Flow
```
User Authentication:
GraphCertificateAuthenticator → Login → Session Management
            ↓                     ↓
    JWT/OAuth2 Tokens → User Session → Role-Based Access
```

### Data Access Pattern
```
Application Layer → Database (Singleton) → MySQL/MariaDB
                         ↓
                 MySQLi + PDO Support
```

## Usage Guidelines

### For Developers
1. **Start with Core Classes**: Begin with Database, Login, and GraphClient for system understanding
2. **Follow Integration Points**: Each document includes integration points with other classes
3. **Security Considerations**: Pay attention to security features and best practices documented
4. **Error Handling**: Review error handling strategies for robust application development

### For System Administrators
1. **Authentication Setup**: Review GraphCertificateAuthenticator for certificate configuration
2. **Database Configuration**: Check Database class for connection and security settings
3. **Performance Tuning**: Review performance considerations in each class documentation

### For Business Analysts
1. **Process Flows**: Each document includes detailed process flows for business understanding
2. **Integration Points**: Understand how different components work together
3. **Usage Patterns**: Review common usage scenarios for each class

## Documentation Standards

### Each Process Definition Includes:
- **Overview**: Purpose and role of the class
- **Core Processes**: Detailed method-by-method process descriptions
- **Integration Points**: Dependencies and relationships with other classes
- **Security Features**: Security considerations and implementations
- **Usage Patterns**: Common usage scenarios and examples
- **Error Handling**: Error scenarios and handling strategies
- **Performance Considerations**: Optimization notes and limitations

### Process Flow Format:
1. **Method Purpose**: What the method accomplishes
2. **Process Flow**: Step-by-step execution details
3. **Input/Output**: Parameter and return value specifications
4. **Integration**: How it connects with other system components

## Maintenance Notes

### Keeping Documentation Current:
- Update process definitions when class methods change
- Review integration points when new classes are added
- Update security considerations when authentication changes
- Revise performance notes based on system optimization

### Adding New Process Definitions:
1. Follow the established format and structure
2. Include all standard sections (Overview, Core Processes, etc.)
3. Document integration points with existing classes
4. Update this index file with the new documentation

## Related Documentation
- **Development Guidelines**: See main README.md for development setup
- **API Documentation**: Individual class files contain method-level documentation
- **Testing Guidelines**: See test directory for testing approaches
- **Deployment Notes**: Check deployment documentation for production considerations