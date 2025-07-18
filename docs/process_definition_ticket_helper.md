# TicketHelper Class Process Definition

## Overview
The `TicketHelper` class is a utility class that provides core functionality for ticket creation, initialization, and string manipulation related to ticket markers. It serves as a factory for creating new tickets and handles ticket-related string operations.

## Class Location
- **File**: `src/Core/TicketHelper.class.php`
- **Type**: Static utility class
- **Dependencies**: TicketController, TicketTooling, SlaHelper, TicketNumberHelper, CategoryHelper

## Core Processes

### 1. Ticket Creation from Email
**Method**: `createNewTicketFromGraphMail()`
- **Purpose**: Creates a new ticket from an incoming Microsoft Graph email
- **Process Flow**:
  1. Creates an empty unsaved ticket using `createEmptyUnsavedTicket()`
  2. Populates ticket fields from GraphMail object:
     - Sets conversation ID from email
     - Extracts sender name and email
     - Strips HTML tags from email subject
  3. Saves the ticket using TicketController
  4. Updates the mail record with the new ticket ID
  5. Returns the saved ticket object
- **Input**: GraphMail object, Mail object
- **Output**: Ticket object or null

### 2. Empty Ticket Initialization
**Method**: `createEmptyUnsavedTicket()`
- **Purpose**: Creates a new ticket with default values and security tokens
- **Process Flow**:
  1. Creates new Ticket object with ID 0 (unsaved)
  2. Generates three security secrets of different lengths (12, 16, 24 chars)
  3. Sets initial due date using SLA helper
  4. Assigns next available ticket number
  5. Sets default category ID
  6. Initializes assignee and status as null
- **Input**: None
- **Output**: Unsaved Ticket object with defaults

### 3. Ticket Marker Detection
**Method**: `stringContainsATicketMarker()`
- **Purpose**: Checks if a string contains a ticket marker pattern
- **Process Flow**:
  1. Uses regex pattern to match ticket markers: `[[##[alphanumeric]##]]`
  2. Returns boolean indicating presence of marker
- **Pattern**: `/\[\[\#\#([0-9A-Za-z]{10,})\#\#\]\]/`
- **Input**: String (nullable)
- **Output**: Boolean

### 4. Ticket Marker Removal
**Method**: `removeTicketMarkerFromString()`
- **Purpose**: Removes ticket markers from strings (e.g., email subjects)
- **Process Flow**:
  1. Uses regex replacement to remove ticket marker pattern
  2. Returns cleaned string
- **Input**: String with potential ticket markers
- **Output**: String without ticket markers

### 5. Ticket Marker Extraction
**Method**: `extractTicketMarkerFromString()`
- **Purpose**: Extracts the complete ticket marker from a string
- **Process Flow**:
  1. Uses regex matching to find ticket marker
  2. Returns the full marker including brackets
- **Input**: String containing ticket marker
- **Output**: Full ticket marker string or null

### 6. Ticket Number Extraction
**Method**: `extractTicketNumberFromString()`
- **Purpose**: Extracts only the ticket number from a ticket marker
- **Process Flow**:
  1. Uses regex matching to find ticket marker
  2. Returns only the alphanumeric ticket number (without brackets)
- **Input**: String containing ticket marker
- **Output**: Ticket number string or null

## Integration Points

### Dependencies
- **TicketController**: For saving tickets to database
- **TicketTooling**: For generating security secrets
- **SlaHelper**: For calculating due dates
- **TicketNumberHelper**: For generating sequential ticket numbers
- **CategoryHelper**: For default category assignment

### Data Structures
- **GraphMail**: Microsoft Graph email structure
- **Mail**: Internal mail object
- **Ticket**: Core ticket entity

## Security Features
- Generates multiple security secrets for ticket protection
- Uses secure random generation for secrets
- Validates ticket marker patterns to prevent injection

## Usage Patterns
1. **Email-to-Ticket Conversion**: Primary use case for converting incoming emails to support tickets
2. **Manual Ticket Creation**: Creating empty tickets for manual entry
3. **Email Thread Management**: Using ticket markers to track email conversations
4. **Subject Line Processing**: Cleaning and extracting ticket information from email subjects

## Error Handling
- Returns null for failed ticket creation
- Handles nullable inputs gracefully
- Uses regex matching with fallback to null returns