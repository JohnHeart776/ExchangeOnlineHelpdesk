# TicketTooling Class Process Definition

## Overview
The `TicketTooling` class provides essential utility functions for ticket security and data conversion. It handles the generation of secure ticket secrets and conversion of Microsoft Graph email data into ticket comment structures.

## Class Location
- **File**: `src/Core/TicketTooling.class.php`
- **Type**: Static utility class
- **Dependencies**: TicketTextCleaner, TicketComment, Ticket, Struct\GraphMail

## Core Processes

### 1. Secure Ticket Secret Generation
**Method**: `getTicketSecret()`
- **Purpose**: Generates cryptographically secure secrets for ticket protection
- **Process Flow**:
  1. Generates 16 random bytes using `random_bytes(16)`
  2. Converts random bytes to hexadecimal GUID
  3. Creates additional unique identifier using `uniqid("", true)`
  4. Concatenates GUID and unique ID
  5. Creates SHA1 hash of the concatenated values
  6. Returns substring of specified length from the hash
- **Input**: Integer length (default: 12 characters)
- **Output**: String containing secure random secret
- **Security Features**:
  - Uses cryptographically secure random number generation
  - Combines multiple entropy sources (random bytes + unique ID)
  - Applies SHA1 hashing for additional randomization
  - Configurable length for different security requirements

### 2. GraphMail to TicketComment Conversion
**Method**: `convertGraphMailToUnsavedTicketComment()`
- **Purpose**: Converts Microsoft Graph email data into ticket comment structure
- **Process Flow**:
  1. Creates new TicketComment object with ID 0 (unsaved)
  2. Associates comment with provided ticket ID
  3. Sets facility to 'user' (indicating user-generated content)
  4. Cleans email body text using TicketTextCleaner
  5. Stores original GraphMail object as JSON for reference
  6. Returns prepared TicketComment object
- **Input**: 
  - GraphMail object (email data from Microsoft Graph)
  - Ticket object (target ticket for the comment)
- **Output**: Unsaved TicketComment object
- **Data Processing**:
  - Text cleaning and sanitization
  - JSON serialization for data preservation
  - Proper facility classification

## Integration Points

### Dependencies
- **TicketTextCleaner**: For sanitizing and cleaning email body content
- **TicketComment**: Target data structure for comments
- **Ticket**: Parent ticket entity
- **Struct\GraphMail**: Microsoft Graph email data structure

### Security Considerations
- **Cryptographic Security**: Uses PHP's `random_bytes()` for secure randomness
- **Multiple Entropy Sources**: Combines different randomness sources
- **Hash-based Obfuscation**: SHA1 hashing prevents direct entropy exposure
- **Configurable Security Levels**: Different secret lengths for various use cases

## Usage Patterns

### 1. Ticket Security Token Generation
- Used by TicketHelper for creating multiple security secrets
- Different lengths for different security purposes:
  - 12 characters: Basic security token
  - 16 characters: Enhanced security token  
  - 24 characters: Maximum security token

### 2. Email-to-Comment Processing
- Primary use case in email processing workflows
- Converts incoming emails to structured ticket comments
- Preserves original email data while creating clean comment text
- Maintains audit trail through JSON storage

## Technical Specifications

### Secret Generation Algorithm
```
1. Generate 16 random bytes
2. Convert to hexadecimal (32 chars)
3. Generate unique ID with microseconds
4. Concatenate: hex_guid + unique_id
5. Apply SHA1 hash (40 chars)
6. Return first N characters
```

### Comment Conversion Mapping
- **TicketId**: From provided Ticket object
- **Facility**: Always set to 'user'
- **Text**: Cleaned email body content
- **GraphObject**: JSON serialized GraphMail data

## Error Handling
- Relies on PHP's built-in random generation error handling
- TicketTextCleaner handles text processing errors
- JSON serialization provides fallback data preservation

## Performance Considerations
- Lightweight operations with minimal computational overhead
- Single-pass text cleaning process
- Efficient JSON serialization
- No database operations (returns unsaved objects)

## Data Integrity
- Preserves original email data in JSON format
- Maintains relationship between tickets and comments
- Ensures proper facility classification for audit purposes
- Provides clean, sanitized text for display while keeping raw data