# GraphCertificateAuthenticator Class Process Definition

## Overview
The `GraphCertificateAuthenticator` class implements certificate-based authentication for Microsoft Graph API access. It extends the BaseAuthenticator class and provides secure, automated authentication using X.509 certificates and JWT assertions for service-to-service authentication scenarios.

## Class Location
- **File**: `src/Auth/GraphCertificateAuthenticator.class.php`
- **Namespace**: `Auth`
- **Type**: Instance-based authenticator class
- **Parent Class**: BaseAuthenticator
- **Dependencies**: Logger, Exception handling, OpenSSL functions

## Core Processes

### 1. Authenticator Initialization
**Method**: `__construct()`
- **Purpose**: Initializes the certificate authenticator with Azure AD application credentials
- **Process Flow**:
  1. Accepts tenant ID, client ID, certificate, private key, and optional key passphrase
  2. Stores authentication parameters for later use
  3. Calls loadKeys() to validate and prepare certificate materials
- **Input**: 
  - Tenant ID (Azure AD tenant identifier)
  - Client ID (Azure AD application ID)
  - Certificate (X.509 certificate content)
  - Private Key (RSA private key content)
  - Key Passphrase (optional, for encrypted keys)
- **Output**: Configured authenticator instance
- **Validation**: Ensures all required parameters are provided

### 2. Certificate and Key Management
**Method**: `loadKeys()`
- **Purpose**: Validates and prepares certificate and private key materials
- **Process Flow**:
  1. Validates X.509 certificate format and content
  2. Loads and validates private key (with optional passphrase)
  3. Verifies certificate-key pair compatibility
  4. Prepares materials for JWT signing operations
- **Input**: Uses instance properties set in constructor
- **Output**: Void (validates and prepares internal state)
- **Security**: Validates certificate integrity and key compatibility
- **Error Handling**: Throws exceptions for invalid certificates or keys

### 3. Access Token Acquisition
**Method**: `getAccessToken()`
- **Purpose**: Obtains Microsoft Graph API access token using certificate authentication
- **Process Flow**:
  1. Creates JWT assertion using createJwtAssertion()
  2. Constructs OAuth2 client credentials grant request
  3. Sends token request to Azure AD token endpoint
  4. Processes token response and extracts access token
  5. Handles authentication errors and token validation
- **Input**: None (uses instance configuration)
- **Output**: String access token for Graph API calls
- **OAuth2 Flow**: Client credentials grant with JWT assertion
- **Error Handling**: Comprehensive error handling for authentication failures

### 4. JWT Assertion Creation
**Method**: `createJwtAssertion()`
- **Purpose**: Creates signed JWT assertion for Azure AD authentication
- **Process Flow**:
  1. Constructs JWT header with algorithm and certificate thumbprint
  2. Creates JWT payload with claims (issuer, subject, audience, expiration)
  3. Encodes header and payload using base64URL encoding
  4. Signs JWT using private key and RS256 algorithm
  5. Combines header, payload, and signature into final JWT
- **Input**: None (uses instance certificate and key)
- **Output**: String JWT assertion
- **Security Features**:
  - RS256 signing algorithm
  - Certificate thumbprint validation
  - Proper claim structure for Azure AD
  - Time-based expiration

### 5. Base64URL Encoding Utility
**Method**: `base64UrlEncode()`
- **Purpose**: Provides JWT-compliant base64URL encoding
- **Process Flow**:
  1. Performs standard base64 encoding
  2. Removes padding characters (=)
  3. Replaces URL-unsafe characters (+, /) with URL-safe equivalents (-, _)
- **Input**: Raw data string
- **Output**: Base64URL encoded string
- **Compliance**: RFC 7515 (JSON Web Signature) specification

### 6. Organization Information Retrieval
**Method**: `getOrganizationInfo()`
- **Purpose**: Retrieves Azure AD organization details using acquired access token
- **Process Flow**:
  1. Uses current access token to call Graph API
  2. Queries organization endpoint for tenant information
  3. Processes and returns organization metadata
  4. Handles API errors and missing data scenarios
- **Input**: None (uses current access token)
- **Output**: Array containing organization information
- **API Endpoint**: Microsoft Graph /organization endpoint
- **Data Retrieved**: Organization name, domain, tenant details

## Integration Points

### Azure AD Integration
- **OAuth2 Client Credentials Flow**: Standard enterprise authentication pattern
- **Certificate-Based Authentication**: High-security authentication method
- **JWT Assertions**: Industry-standard token format for service authentication
- **Token Endpoint**: Azure AD v2.0 token endpoint integration

### Microsoft Graph API Integration
- **Access Token Usage**: Provides tokens for Graph API calls
- **Organization Queries**: Retrieves tenant organization information
- **Scope Management**: Handles appropriate API scopes for operations

### Application Integration
- **BaseAuthenticator Extension**: Follows established authenticator patterns
- **Logger Integration**: Comprehensive logging for authentication events
- **Error Handling**: Consistent exception handling across authentication flows

## Security Features

### Certificate Security
- **X.509 Certificate Validation**: Ensures certificate integrity and format
- **Private Key Protection**: Secure handling of private key materials
- **Key Passphrase Support**: Supports encrypted private keys
- **Certificate-Key Pair Validation**: Ensures matching certificate and key

### JWT Security
- **RS256 Signing**: Industry-standard asymmetric signing algorithm
- **Certificate Thumbprint**: Binds JWT to specific certificate
- **Time-Based Expiration**: Prevents token replay attacks
- **Proper Claims Structure**: Follows Azure AD JWT requirements

### Token Security
- **Secure Token Storage**: Proper handling of access tokens
- **Token Validation**: Validates received tokens from Azure AD
- **Error Information Protection**: Prevents sensitive data exposure in errors

## Usage Patterns

### 1. Service Authentication Setup
```php
$authenticator = new GraphCertificateAuthenticator(
    $tenantId,
    $clientId, 
    $certificate,
    $privateKey,
    $keyPassphrase
);
```

### 2. Token Acquisition
```php
$accessToken = $authenticator->getAccessToken();
// Use token for Graph API calls
```

### 3. Organization Information Retrieval
```php
$orgInfo = $authenticator->getOrganizationInfo();
// Process organization details
```

## Error Scenarios

### Certificate-Related Errors
- **Invalid Certificate Format**: Malformed X.509 certificates
- **Certificate-Key Mismatch**: Non-matching certificate and private key pairs
- **Expired Certificates**: Certificates past their validity period
- **Key Decryption Failures**: Incorrect passphrases for encrypted keys

### Authentication Errors
- **Invalid Client Credentials**: Incorrect tenant ID or client ID
- **Certificate Not Registered**: Certificate not associated with Azure AD application
- **Insufficient Permissions**: Application lacks required Graph API permissions
- **Network Connectivity**: Issues connecting to Azure AD endpoints

### JWT Creation Errors
- **Signing Failures**: Problems with private key signing operations
- **Invalid Claims**: Malformed JWT payload or claims
- **Encoding Issues**: Problems with base64URL encoding

## Performance Considerations

### Certificate Operations
- **Key Loading Optimization**: Certificates and keys loaded once during initialization
- **Signing Performance**: RSA signing operations are computationally intensive
- **Certificate Caching**: Validated certificates cached for reuse

### Token Management
- **Token Caching**: Access tokens cached until expiration
- **Refresh Strategy**: Proactive token refresh before expiration
- **Network Optimization**: Minimizes authentication requests to Azure AD

### Security vs Performance Balance
- **Certificate Validation**: Thorough validation balanced with performance needs
- **JWT Creation**: Optimized JWT creation while maintaining security
- **Error Handling**: Comprehensive error handling without performance impact

## Compliance and Standards
- **RFC 7515**: JSON Web Signature (JWS) compliance
- **RFC 7519**: JSON Web Token (JWT) compliance  
- **OAuth 2.0**: Client credentials grant flow implementation
- **X.509**: Standard certificate format support
- **Azure AD Requirements**: Full compliance with Azure AD authentication requirements