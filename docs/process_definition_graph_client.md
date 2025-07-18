# GraphClient Class Process Definition

## Overview
The `GraphClient` class is the primary interface for Microsoft Graph API interactions within the Exchange Online Helpdesk application. It handles authentication, HTTP communications, user management, email operations, and data conversion between Microsoft Graph and internal application formats.

## Class Location
- **File**: `src/Client/GraphClient.class.php`
- **Namespace**: `Client`
- **Type**: Instance-based service class
- **Dependencies**: Auth\GraphCertificateAuthenticator, CurlHelper, Struct\GraphMail, Struct\GraphUser, etc.

## Core Processes

### 1. Authentication and Token Management
**Method**: `__construct()`
- **Purpose**: Initializes GraphClient with certificate-based authentication
- **Process Flow**:
  1. Accepts GraphCertificateAuthenticator instance
  2. Stores authenticator for token management
- **Input**: GraphCertificateAuthenticator object
- **Output**: Configured GraphClient instance

**Method**: `getAccessToken()`
- **Purpose**: Retrieves current Microsoft Graph access token
- **Process Flow**:
  1. Delegates to authenticator for token retrieval
  2. Returns valid access token for API calls
- **Input**: None
- **Output**: String access token

### 2. HTTP Request Management
**Method**: `getRequest()`
- **Purpose**: Executes HTTP GET requests to Microsoft Graph API
- **Process Flow**:
  1. Prepares authorization headers with access token
  2. Merges additional headers if provided
  3. Uses CurlHelper for HTTP communication
  4. Returns raw response data
- **Input**: URL string, optional additional headers array
- **Output**: String response data

**Method**: `postJsonRequest()`
- **Purpose**: Executes HTTP POST requests with JSON payloads
- **Process Flow**:
  1. Prepares authorization and content-type headers
  2. Serializes payload to JSON
  3. Executes POST request via CurlHelper
  4. Returns response data
- **Input**: URL string, payload array, optional headers
- **Output**: String response data

**Method**: `patchJsonRequest()`
- **Purpose**: Executes HTTP PATCH requests for data updates
- **Process Flow**:
  1. Similar to POST but uses PATCH method
  2. Used for partial resource updates
  3. Handles JSON serialization and headers
- **Input**: URL string, payload array, optional headers
- **Output**: String response data

### 3. User Management Operations
**Method**: `getUserInfo()`
- **Purpose**: Retrieves user information from Microsoft Graph
- **Process Flow**:
  1. Constructs Graph API URL for user endpoint
  2. Executes GET request for user data
  3. Converts JSON response to GraphUser object
  4. Handles user not found scenarios
- **Input**: User Principal Name (UPN) string
- **Output**: GraphUser object or null

**Method**: `getUserImage()`
- **Purpose**: Fetches user profile image from Microsoft Graph
- **Process Flow**:
  1. Constructs photo endpoint URL
  2. Executes GET request for image data
  3. Handles different image formats and sizes
  4. Creates GraphUserImage object with metadata
- **Input**: User Principal Name (UPN) string
- **Output**: GraphUserImage object or null

**Method**: `fetchUserPage()`
- **Purpose**: Retrieves paginated user data collections
- **Process Flow**:
  1. Executes GET request to provided URL
  2. Parses JSON response for user collection
  3. Returns array of user data
- **Input**: Graph API URL string
- **Output**: Array of user data

### 4. Email Operations
**Method**: `sendMailAsUser()`
- **Purpose**: Sends simple email messages on behalf of a user
- **Process Flow**:
  1. Constructs email message structure
  2. Formats recipients and content
  3. Uses Graph API sendMail endpoint
  4. Returns success/failure status
- **Input**: UPN, subject, body, recipients array
- **Output**: Boolean success status

**Method**: `sendMultipartMailAsUser()`
- **Purpose**: Sends email messages with file attachments
- **Process Flow**:
  1. Creates complex message structure with attachments
  2. Encodes attachments as base64
  3. Constructs multipart message format
  4. Executes send operation via Graph API
- **Input**: UPN, subject, body, recipients, attachments array
- **Output**: Boolean success status

**Method**: `fetchMails()`
- **Purpose**: Retrieves email messages from user's mailbox
- **Process Flow**:
  1. Constructs mailbox query with filters and limits
  2. Executes GET request to messages endpoint
  3. Processes response data into GraphMail objects
  4. Handles pagination and result limits
- **Input**: User email, maximum count
- **Output**: Array of GraphMail objects

**Method**: `fetchAttachments()`
- **Purpose**: Retrieves attachments for a specific email message
- **Process Flow**:
  1. Constructs attachment endpoint URL
  2. Fetches attachment metadata and content
  3. Creates GraphMailAttachment objects
  4. Handles different attachment types
- **Input**: User email, message ID
- **Output**: Array of GraphMailAttachment objects

### 5. Email Content Management
**Method**: `getMailFromAzureAsJsonObject()`
- **Purpose**: Retrieves raw email data as JSON object
- **Process Flow**:
  1. Constructs specific message endpoint URL
  2. Executes GET request for complete message data
  3. Returns parsed JSON response
- **Input**: User email, mail ID
- **Output**: Array (parsed JSON)

**Method**: `getMailFromAzureAsGraphMail()`
- **Purpose**: Retrieves email and converts to GraphMail object
- **Process Flow**:
  1. Fetches raw JSON data using getMailFromAzureAsJsonObject()
  2. Converts JSON to structured GraphMail object
  3. Handles data transformation and validation
- **Input**: Mailbox string, mail ID
- **Output**: GraphMail object

### 6. Email Subject Manipulation
**Method**: `prefixMailSubject()`
- **Purpose**: Adds prefix to email subject line
- **Process Flow**:
  1. Retrieves current email subject
  2. Prepends specified prefix
  3. Updates email via PATCH request
- **Input**: Mailbox, mail ID, prefix string
- **Output**: Void (updates email in place)

**Method**: `suffixMailSubject()`
- **Purpose**: Adds suffix to email subject line
- **Process Flow**:
  1. Retrieves current email subject
  2. Appends specified suffix
  3. Updates email via PATCH request
- **Input**: Mailbox, mail ID, suffix string
- **Output**: Void (updates email in place)

**Method**: `updateMailSubject()`
- **Purpose**: Completely replaces email subject line
- **Process Flow**:
  1. Constructs PATCH request with new subject
  2. Updates email message properties
  3. Executes update via Graph API
- **Input**: Mailbox, mail ID, new subject string
- **Output**: Void (updates email in place)

## Integration Points

### Authentication Integration
- **GraphCertificateAuthenticator**: Provides OAuth2 certificate-based authentication
- **Token Management**: Handles token refresh and validation
- **Security Headers**: Manages authorization headers for API calls

### Data Structure Integration
- **GraphMail**: Converts Graph API email data to internal format
- **GraphUser**: Transforms user data from Graph API
- **GraphMailAttachment**: Handles email attachment data
- **GraphUserImage**: Manages user profile image data

### HTTP Communication
- **CurlHelper**: Underlying HTTP client for API communications
- **Error Handling**: Manages HTTP errors and API responses
- **Header Management**: Handles authentication and content-type headers

## Security Features

### Authentication Security
- **Certificate-Based Auth**: Uses X.509 certificates for secure authentication
- **Token Validation**: Ensures valid tokens for all API calls
- **Secure Headers**: Proper authorization header management

### Data Protection
- **Input Validation**: Validates user inputs and API parameters
- **Output Sanitization**: Cleans data received from Graph API
- **Error Information**: Prevents sensitive data exposure in errors

## Usage Patterns

### 1. User Information Retrieval
1. Authenticate with certificate
2. Call `getUserInfo()` with UPN
3. Optionally fetch user image with `getUserImage()`
4. Process returned GraphUser object

### 2. Email Processing Workflow
1. Fetch emails using `fetchMails()`
2. Process each GraphMail object
3. Retrieve attachments if needed
4. Update email subjects for tracking

### 3. Email Sending Operations
1. Prepare message content and recipients
2. Use `sendMailAsUser()` for simple messages
3. Use `sendMultipartMailAsUser()` for messages with attachments
4. Handle success/failure responses

## Performance Considerations

### API Efficiency
- **Batch Operations**: Minimizes API calls where possible
- **Selective Fields**: Requests only needed data fields
- **Pagination Handling**: Manages large result sets efficiently

### Caching Strategy
- **Token Caching**: Reuses valid access tokens
- **Response Caching**: May cache frequently accessed data
- **Connection Reuse**: Leverages HTTP connection pooling

### Error Handling
- **Retry Logic**: Handles transient API failures
- **Rate Limiting**: Respects Graph API throttling
- **Graceful Degradation**: Continues operation when possible

## API Endpoints Used

### User Endpoints
- `/users/{upn}` - User information
- `/users/{upn}/photo` - User profile images
- `/users` - User collections

### Mail Endpoints
- `/users/{upn}/messages` - Email messages
- `/users/{upn}/messages/{id}` - Specific email
- `/users/{upn}/messages/{id}/attachments` - Email attachments
- `/users/{upn}/sendMail` - Send email operation

## Error Scenarios
- **Authentication Failures**: Invalid certificates or expired tokens
- **API Rate Limiting**: Too many requests to Graph API
- **Network Issues**: Connectivity problems with Microsoft services
- **Data Format Errors**: Invalid JSON or unexpected response formats
- **Permission Issues**: Insufficient Graph API permissions