# Login Class Process Definition

## Overview
The `Login` class is the core authentication and session management system for the Exchange Online Helpdesk application. It handles user authentication, session lifecycle, token management, role-based access control, and navigation flow control.

## Class Location
- **File**: `src/Core/Login.class.php`
- **Type**: Static utility class
- **Dependencies**: Client\GraphClient, Struct\GraphUserLoginResponse, User class

## Core Processes

### 1. Session Status Management
**Method**: `isLoggedIn()`
- **Purpose**: Determines if a user is currently authenticated
- **Process Flow**:
  1. Checks for presence of user session data
  2. Returns boolean indicating authentication status
- **Input**: None
- **Output**: Boolean (true if authenticated)

**Method**: `getUser()`
- **Purpose**: Retrieves the currently authenticated user object
- **Process Flow**:
  1. Checks if user is logged in
  2. Returns User object from session or null if not authenticated
- **Input**: None
- **Output**: User object or null

### 2. Token Management
**Method**: `getAccessToken()`
- **Purpose**: Retrieves the current Microsoft Graph access token
- **Process Flow**:
  1. Extracts access token from session data
  2. Returns token string or null if unavailable
- **Input**: None
- **Output**: String token or null

**Method**: `refreshAccessTokenIfNeeded()`
- **Purpose**: Automatically refreshes expired or near-expired access tokens
- **Process Flow**:
  1. Checks token expiration time against refresh threshold
  2. If refresh needed, uses refresh token to obtain new access token
  3. Updates session with new token data
  4. Handles refresh failures gracefully
- **Input**: None
- **Output**: Void (updates session internally)
- **Threshold**: Configurable via `getTokenRefreshThreshold()`

**Method**: `getTokenRefreshThreshold()`
- **Purpose**: Defines when tokens should be refreshed before expiration
- **Process Flow**:
  1. Returns time threshold in seconds before expiration
  2. Used by refresh logic to determine refresh timing
- **Input**: None
- **Output**: Integer (seconds before expiration)

### 3. Session Lifecycle Management
**Method**: `initSession()`
- **Purpose**: Initializes a new user session with security settings
- **Process Flow**:
  1. Starts PHP session with secure configuration
  2. Sets session security parameters
  3. Initializes session variables
- **Input**: None
- **Output**: Void

**Method**: `startSession()`
- **Purpose**: Establishes authenticated session for a user
- **Process Flow**:
  1. Stores user object in session
  2. Saves access token and refresh token
  3. Records token expiration time
  4. Sets session as authenticated
- **Input**: User object, access token, refresh token, expiration time
- **Output**: Void

**Method**: `logout()`
- **Purpose**: Terminates user session and clears authentication data
- **Process Flow**:
  1. Clears all session variables
  2. Destroys PHP session
  3. Removes authentication cookies
  4. Redirects to login page
- **Input**: None
- **Output**: Void (performs redirect)

### 4. User Authentication from Graph
**Method**: `loginUserFromGraphResponse()`
- **Purpose**: Creates or updates user account from Microsoft Graph authentication
- **Process Flow**:
  1. Extracts user data from Graph API response
  2. Searches for existing user by email/UPN
  3. Creates new user if not found, updates if exists
  4. Processes organization information
  5. Saves user data to database
  6. Returns authenticated User object
- **Input**: Graph data array, organization info, access token, refresh token
- **Output**: User object

### 5. Role-Based Access Control
**Method**: `isGuest()`, `isUser()`, `isAgent()`, `isAdmin()`
- **Purpose**: Determine user's role and permissions level
- **Process Flow**:
  1. Checks user authentication status
  2. Evaluates user's role assignments
  3. Returns boolean indicating role membership
- **Input**: None
- **Output**: Boolean for each role check

**Method**: `requireIsGuest()`, `requireIsUser()`, `requireIsAgent()`, `requireIsAdmin()`
- **Purpose**: Enforce role-based access requirements
- **Process Flow**:
  1. Checks if user has required role
  2. If not authorized, redirects to appropriate page
  3. May display error message
- **Input**: None
- **Output**: Void (may redirect)

### 6. Access Control Enforcement
**Method**: `requireLogin()`
- **Purpose**: Ensures user is authenticated before accessing protected resources
- **Process Flow**:
  1. Checks authentication status
  2. If not logged in, redirects to login page
  3. Preserves original destination for post-login redirect
- **Input**: None
- **Output**: Void (may redirect)

### 7. Navigation Control
**Method**: `bringToDashboard()`
- **Purpose**: Redirects authenticated users to main dashboard
- **Process Flow**:
  1. Performs redirect to dashboard URL
  2. Terminates current script execution
- **Input**: None
- **Output**: Void (performs redirect)

**Method**: `bringToLogin()`
- **Purpose**: Redirects users to login page with optional parameters
- **Process Flow**:
  1. Constructs login URL with optional redirect target
  2. Includes optional message parameter
  3. Can use current page as redirect target
  4. Performs redirect and terminates execution
- **Input**: Optional redirect target, use current page flag, message
- **Output**: Void (performs redirect)

## Integration Points

### Microsoft Graph Integration
- **GraphClient**: Uses GraphClient for API communications
- **Token Management**: Handles OAuth2 token lifecycle
- **User Data Sync**: Synchronizes user data from Azure AD

### Database Integration
- **User Persistence**: Saves and updates user records
- **Session Storage**: May use database for session persistence
- **Organization Data**: Manages organization information

### Session Management
- **PHP Sessions**: Utilizes PHP's native session handling
- **Security Configuration**: Implements secure session parameters
- **Cross-Request Persistence**: Maintains state across HTTP requests

## Security Features

### Token Security
- **Automatic Refresh**: Prevents token expiration issues
- **Secure Storage**: Tokens stored in server-side sessions
- **Expiration Handling**: Proper token lifecycle management

### Session Security
- **Secure Configuration**: Implements security best practices
- **Session Validation**: Validates session integrity
- **Logout Protection**: Complete session cleanup on logout

### Access Control
- **Role-Based Authorization**: Multi-level permission system
- **Redirect Protection**: Prevents unauthorized access
- **Authentication Requirements**: Enforces login requirements

## Usage Patterns

### 1. Authentication Flow
1. User initiates login process
2. Microsoft Graph authentication
3. `loginUserFromGraphResponse()` processes user data
4. `startSession()` establishes authenticated session
5. User redirected to dashboard

### 2. Request Protection
1. Protected pages call `requireLogin()`
2. Role-specific pages call appropriate `requireIs*()` methods
3. Unauthorized users redirected appropriately

### 3. Token Maintenance
1. Each request may trigger `refreshAccessTokenIfNeeded()`
2. Automatic token refresh maintains API access
3. Failed refresh triggers re-authentication

## Error Handling
- **Token Refresh Failures**: Graceful degradation to re-authentication
- **Session Corruption**: Automatic session cleanup and redirect
- **Database Errors**: Proper error propagation and logging
- **Graph API Errors**: Fallback authentication mechanisms

## Performance Considerations
- **Session Efficiency**: Minimal session data storage
- **Token Caching**: Avoids unnecessary API calls
- **Lazy Loading**: User data loaded only when needed
- **Redirect Optimization**: Minimal redirect chains