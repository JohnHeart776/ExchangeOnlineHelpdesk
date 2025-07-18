# Exchange Online Ticket System EOTS - Advanced Ticket Management System

EOTS is a comprehensive web-based ticket management system designed for organizations using Microsoft 365. It automatically fetches emails from designated mailboxes, converts them into support tickets, and provides a powerful interface for agents and users to manage and track ticket resolution.

## Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [System Architecture](#system-architecture)
4. [Prerequisites](#prerequisites)
5. [Azure Application Setup](#azure-application-setup)
6. [Installation](#installation)
7. [Configuration](#configuration)
8. [Usage](#usage)
9. [API Documentation](#api-documentation)
10. [Troubleshooting](#troubleshooting)
11. [Contributing](#contributing)
12. [License](#license)

## Overview

EOTS is an enterprise-grade ticket management solution that seamlessly integrates with Microsoft 365 environments. The system automatically processes incoming emails, creates tickets, and provides role-based access for both support agents and end users.

### What This Project Does

- **Automated Email Processing**: Fetches emails from Microsoft 365 mailboxes and converts them into structured tickets
- **Microsoft Authentication**: Secure login using Microsoft Azure AD OAuth2
- **Role-Based Access Control**: Different interfaces for agents, administrators, and end users
- **AI-Powered Features**: Automatic ticket categorization, response generation, and content analysis
- **Comprehensive Reporting**: Built-in reporting and analytics capabilities
- **Multi-tenant Support**: Supports multiple organizations and user groups

### How It Works

1. **Email Ingestion**: The system connects to Microsoft Graph API to fetch emails from designated mailboxes
2. **Ticket Creation**: Emails are automatically parsed and converted into tickets with proper categorization
3. **User Authentication**: Users log in using their Microsoft 365 credentials via OAuth2
4. **Ticket Management**: Agents can view, update, and resolve tickets through a web interface
5. **Notifications**: Automatic email notifications keep users informed of ticket updates
6. **Reporting**: Generate reports on ticket metrics, response times, and resolution statistics

## Key Features

### Core Functionality
- **Automatic Email-to-Ticket Conversion**: Seamlessly converts emails into structured tickets
- **Microsoft Graph Integration**: Full integration with Microsoft 365 for email and user management
- **Role-Based Dashboards**: Customized interfaces for agents, users, and administrators
- **Real-time Notifications**: Email notifications for ticket updates and status changes
- **Advanced Search**: Powerful search capabilities across tickets, users, and attachments
- **File Attachment Support**: Handle email attachments and associate them with tickets

### Advanced Features
- **AI Integration**: OpenAI and Azure AI integration for automated responses and categorization
- **SLA Management**: Service Level Agreement tracking and monitoring
- **Custom Categories**: Flexible ticket categorization system
- **Reporting Engine**: Comprehensive reporting and analytics
- **API Access**: RESTful API for integration with external systems
- **Multi-language Support**: Internationalization support (primarily German)

### Technical Features
- **PHP 8+ Backend**: Modern PHP architecture with object-oriented design
- **Smarty Templating**: Clean separation of logic and presentation
- **MariaDB/MySQL Database**: Robust database backend with proper indexing
- **Certificate-based Authentication**: Secure certificate-based authentication for Graph API
- **Responsive Design**: Mobile-friendly interface using modern CSS frameworks

## System Architecture

### High-Level Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Microsoft     │    │   EOTS           │    │   Database      │
│   Graph API     │◄──►│   Application    │◄──►│   (MariaDB)     │
│                 │    │                  │    │                 │
│ • Mail Fetching │    │ • Ticket Mgmt    │    │ • Tickets       │
│ • User Auth     │    │ • User Interface │    │ • Users         │
│ • Send Emails   │    │ • API Endpoints  │    │ • Config        │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌──────────────────┐
                       │   AI Services    │
                       │                  │
                       │ • OpenAI API     │
                       │ • Azure AI       │
                       │ • Auto-responses │
                       └──────────────────┘
```

### Detailed System Components

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           EOTS Application Layer                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  Web Interface Layer                                                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Dashboard  │  │   Tickets   │  │   Reports   │  │    Admin    │        │
│  │   (PHP)     │  │   (PHP)     │  │   (PHP)     │  │   (PHP)     │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘        │
├─────────────────────────────────────────────────────────────────────────────┤
│  API Layer                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   /api/me   │  │ /api/user   │  │ /api/agent  │  │ /api/admin  │        │
│  │  (REST)     │  │  (REST)     │  │  (REST)     │  │  (REST)     │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘        │
├─────────────────────────────────────────────────────────────────────────────┤
│  Business Logic Layer                                                       │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Ticket    │  │    Mail     │  │    User     │  │  Category   │        │
│  │   Manager   │  │  Processor  │  │   Manager   │  │   Manager   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘        │
├─────────────────────────────────────────────────────────────────────────────┤
│  Data Access Layer                                                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Database   │  │   Config    │  │    Auth     │  │   Traits    │        │
│  │   Classes   │  │   Manager   │  │   Manager   │  │  (Shared)   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘        │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Data Structure and Relationships

### Core Entity Relationship Diagram

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│      User       │    │   Organization  │    │    Category     │
│                 │    │      User       │    │                 │
│ • UserId (PK)   │    │                 │    │ • CategoryId    │
│ • Guid          │    │ • OrgUserId     │    │ • InternalName  │
│ • AzureObjectId │    │ • AzureObjectId │    │ • PublicName    │
│ • DisplayName   │    │ • DisplayName   │    │ • Icon/Color    │
│ • UserRole      │    │ • Mail          │    │ • IsDefault     │
│ • TenantId      │    │ • Department    │    └─────────────────┘
└─────────────────┘    └─────────────────┘             │
         │                       │                     │
         │                       │                     │
         ▼                       ▼                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                           Ticket                                │
│                                                                 │
│ • TicketId (PK)              • StatusId (FK)                   │
│ • Guid                       • CategoryId (FK)                 │
│ • TicketNumber               • AssigneeUserId (FK)             │
│ • ConversationId             • CreatedDatetime                 │
│ • Subject                    • UpdatedDatetime                 │
│ • MessengerName/Email        • DueDatetime                     │
└─────────────────────────────────────────────────────────────────┘
         │                       │                     │
         ▼                       ▼                     ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ TicketComment   │    │  TicketStatus   │    │   TicketFile    │
│                 │    │                 │    │                 │
│ • CommentId     │    │ • StatusId      │    │ • FileId        │
│ • TicketId (FK) │    │ • TicketId (FK) │    │ • TicketId (FK) │
│ • UserId (FK)   │    │ • OldStatusId   │    │ • UserId (FK)   │
│ • Text          │    │ • NewStatusId   │    │ • AccessLevel   │
│ • AccessLevel   │    │ • UserId (FK)   │    │ • CreatedAt     │
│ • Facility      │    │ • Comment       │    └─────────────────┘
│ • MailId (FK)   │    │ • CreatedAt     │
└─────────────────┘    └─────────────────┘
         │
         ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│      Mail       │    │ MailAttachment  │    │     Status      │
│                 │    │                 │    │                 │
│ • MailId (PK)   │    │ • AttachmentId  │    │ • StatusId (PK) │
│ • AzureId       │    │ • MailId (FK)   │    │ • InternalName  │
│ • MessageId     │    │ • Name          │    │ • PublicName    │
│ • TicketId (FK) │    │ • ContentType   │    │ • Color/Icon    │
│ • Subject       │    │ • Size          │    │ • IsOpen        │
│ • SenderEmail   │    │ • Content       │    │ • IsFinal       │
│ • Body          │    │ • HashSha256    │    │ • IsDefault     │
│ • ConversationId│    └─────────────────┘    └─────────────────┘
└─────────────────┘
```

### Data Flow Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          Email Processing Flow                              │
└─────────────────────────────────────────────────────────────────────────────┘

Microsoft Graph API
         │
         ▼ (Fetch Emails)
┌─────────────────┐
│   GraphMail     │ ──────► Parse email headers, body, attachments
│   Structure     │         Extract conversation ID, sender info
└─────────────────┘
         │
         ▼ (Convert)
┌─────────────────┐
│   Mail Object   │ ──────► Store in Mail table
│   (Database)    │         Link to existing conversation
└─────────────────┘
         │
         ▼ (Process)
┌─────────────────┐
│ Ticket Creation │ ──────► Create new ticket OR
│   or Update     │         Add to existing ticket
└─────────────────┘
         │
         ▼ (Categorize)
┌─────────────────┐
│ AI Categorization│ ─────► Auto-assign category
│ & Classification │        Suggest priority/urgency
└─────────────────┘
         │
         ▼ (Notify)
┌─────────────────┐
│  Notifications  │ ──────► Email notifications
│   & Updates     │         Status updates
└─────────────────┘
```

## Process Flows and Workflows

### Authentication Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Microsoft OAuth2 Authentication                      │
└─────────────────────────────────────────────────────────────────────────────┘

User Access Request
         │
         ▼
┌─────────────────┐
│  Check Session  │ ──No──► Redirect to Microsoft Login
│   (Login.php)   │
└─────────────────┘
         │ Yes
         ▼
┌─────────────────┐
│ Validate Token  │ ──Invalid──► Force Re-authentication
│  & Permissions  │
└─────────────────┘
         │ Valid
         ▼
┌─────────────────┐
│  Load User      │ ──────► Check User Role:
│  Profile &      │         • Guest (limited access)
│  Role           │         • User (own tickets)
└─────────────────┘         • Agent (assigned tickets)
         │                  • Admin (full access)
         ▼
┌─────────────────┐
│ Grant Access    │ ──────► Route to appropriate dashboard
│ to Dashboard    │
└─────────────────┘
```

### Ticket Lifecycle Management

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           Ticket Lifecycle                                 │
└─────────────────────────────────────────────────────────────────────────────┘

Email Received ──────► Mail Processing ──────► Ticket Creation/Update
     │                      │                         │
     ▼                      ▼                         ▼
┌─────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ New Email   │    │ Parse & Store   │    │ Create Ticket   │
│ in Mailbox  │    │ in Mail Table   │    │ or Add Comment  │
└─────────────┘    └─────────────────┘    └─────────────────┘
                            │                         │
                            ▼                         ▼
                   ┌─────────────────┐    ┌─────────────────┐
                   │ Extract         │    │ AI Categorization│
                   │ Attachments     │    │ & Auto-Assignment│
                   └─────────────────┘    └─────────────────┘
                                                   │
                                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Ticket Status Flow                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  New ──────► Assigned ──────► In Progress ──────► Waiting for Customer     │
│   │             │                  │                        │               │
│   │             ▼                  ▼                        ▼               │
│   │         Working ──────► Customer Reply ──────► Resolved ──────► Closed │
│   │                                                    │                    │
│   └─────────────────► Cancelled ◄─────────────────────┘                    │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### User Interaction Workflows

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          User Role Workflows                               │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   End User      │    │     Agent       │    │  Administrator  │
│   Workflow      │    │   Workflow      │    │    Workflow     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ • View own      │    │ • View assigned │    │ • System config │
│   tickets       │    │   tickets       │    │ • User mgmt     │
│ • Create new    │    │ • Update status │    │ • Category mgmt │
│   tickets       │    │ • Add comments  │    │ • Status mgmt   │
│ • Add comments  │    │ • Assign to     │    │ • Reports       │
│ • Upload files  │    │   other agents  │    │ • AI config     │
│ • View status   │    │ • Close tickets │    │ • Mail settings │
└─────────────────┘    │ • Generate      │    │ • Template mgmt │
                       │   responses     │    │ • Audit logs    │
                       └─────────────────┘    └─────────────────┘
```

### AI Integration Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        AI Processing Pipeline                              │
└─────────────────────────────────────────────────────────────────────────────┘

New Email/Ticket Content
         │
         ▼
┌─────────────────┐
│ Content Analysis│ ──────► Extract key information:
│ (OpenAI/Azure)  │         • Subject classification
└─────────────────┘         • Urgency detection
         │                  • Language detection
         ▼                  • Sentiment analysis
┌─────────────────┐
│ Category        │ ──────► Match against:
│ Suggestion      │         • CategorySuggestion rules
└─────────────────┘         • Historical patterns
         │                  • Keyword filters
         ▼
┌─────────────────┐
│ Auto-Response   │ ──────► Generate:
│ Generation      │         • Acknowledgment emails
└─────────────────┘         • Suggested replies
         │                  • Status updates
         ▼
┌─────────────────┐
│ Cache Results   │ ──────► Store in AiCache table
│ & Apply         │         Apply suggestions to ticket
└─────────────────┘
```

## Technical Implementation Details

### Directory Structure and Components

```
src/
├── Application/          # Core business logic classes
│   ├── Ticket.class.php     # Main ticket management
│   ├── Mail.class.php       # Email processing
│   ├── User.class.php       # User management
│   ├── Category.class.php   # Ticket categorization
│   ├── Status.class.php     # Status management
│   └── ...
├── Auth/                 # Authentication and authorization
│   ├── MicrosoftAuth.php    # OAuth2 implementation
│   └── Permissions.php      # Role-based access control
├── Client/               # External API clients
│   ├── GraphClient.php      # Microsoft Graph API client
│   ├── OpenAiClient.php     # OpenAI API integration
│   └── AzureAiClient.php    # Azure AI services
├── Controller/           # MVC controllers
│   ├── TicketController.php # Ticket operations
│   ├── UserController.php   # User operations
│   └── ApiController.php    # API endpoints
├── Core/                 # Core utilities and helpers
│   ├── Database.class.php   # Database abstraction
│   ├── Login.class.php      # Session management
│   ├── DateHelper.class.php # Date/time utilities
│   └── GuidHelper.class.php # GUID generation
├── Struct/               # Data structures
│   ├── GraphMail.class.php  # Email data structure
│   ├── GraphUser.class.php  # User data structure
│   └── Enums/               # Enumeration classes
└── Trait/                # Reusable code traits
    ├── TicketTrait.class.php
    ├── MailTemplateTrait.class.php
    └── JsonSerializableTrait.class.php
```

### API Endpoint Structure

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                            API Endpoints                                   │
└─────────────────────────────────────────────────────────────────────────────┘

/api/me/                  # Current user operations
├── GET    /profile       # Get current user profile
├── GET    /tickets       # Get user's tickets
├── POST   /tickets       # Create new ticket
└── PUT    /tickets/{id}  # Update user's ticket

/api/user/                # User-specific operations
├── GET    /{id}          # Get user details
├── GET    /{id}/tickets  # Get user's tickets
└── POST   /{id}/notify   # Send notification

/api/agent/               # Agent operations
├── GET    /tickets       # Get assigned tickets
├── PUT    /tickets/{id}  # Update ticket status
├── POST   /tickets/{id}/assign  # Assign ticket
├── POST   /tickets/{id}/comment # Add comment
└── GET    /dashboard     # Agent dashboard data

/api/admin/               # Administrative operations
├── GET    /users         # List all users
├── POST   /users         # Create user
├── GET    /categories    # List categories
├── POST   /categories    # Create category
├── GET    /status        # List statuses
├── POST   /status        # Create status
├── GET    /config        # System configuration
└── PUT    /config        # Update configuration

/api/organizationuser/    # Organization user sync
├── GET    /             # List org users
├── POST   /sync         # Sync with Azure AD
└── GET    /{id}         # Get org user details
```

### Database Design Patterns

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         Database Patterns                                  │
└─────────────────────────────────────────────────────────────────────────────┘

1. GUID-based Security
   • Every entity has a GUID for secure external references
   • Primary keys remain internal integers for performance
   • URLs use GUIDs to prevent enumeration attacks

2. Audit Trail Pattern
   • TicketStatus table tracks all status changes
   • CreatedAt/UpdatedAt timestamps on all entities
   • User tracking for all modifications

3. Soft Delete Pattern
   • Enabled/Disabled flags instead of hard deletes
   • Maintains referential integrity
   • Allows for data recovery and audit trails

4. Conversation Threading
   • ConversationId links emails to tickets
   • Maintains email thread continuity
   • Supports multi-participant conversations

5. Access Control Pattern
   • AccessLevel enum (Public/Internal) on comments and files
   • Role-based permissions (Guest/User/Agent/Admin)
   • Hierarchical access control

6. Caching Strategy
   • AiCache table for expensive AI operations
   • Configuration caching in Config table
   • Template caching for email notifications
```

### Security Implementation

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         Security Measures                                  │
└─────────────────────────────────────────────────────────────────────────────┘

Authentication:
├── Microsoft OAuth2 integration
├── JWT token validation
├── Session management with secure cookies
└── Multi-tenant support with TenantId isolation

Authorization:
├── Role-based access control (RBAC)
├── Resource-level permissions
├── API endpoint protection
└── Data filtering based on user role

Data Protection:
├── GUID-based external references
├── SHA256 hashing for file integrity
├── Encrypted file storage with multiple secrets
├── SQL injection prevention with prepared statements
└── XSS protection with input sanitization

Communication Security:
├── HTTPS enforcement
├── Certificate-based Graph API authentication
├── Secure email transmission
└── API rate limiting and throttling
```

## Development and Deployment Guide

### Development Environment Setup

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      Development Workflow                                  │
└─────────────────────────────────────────────────────────────────────────────┘

1. Environment Preparation
   ├── PHP 8.1+ with required extensions
   ├── MariaDB/MySQL database server
   ├── Web server (Apache/Nginx)
   └── SSL certificate for OAuth2 callbacks

2. Database Setup
   ├── Import schema.sql for table structure
   ├── Import category.sql for default categories
   ├── Import status.sql for ticket statuses
   ├── Import config.sql for system configuration
   └── Import menu.sql for navigation structure

3. Configuration
   ├── Set environment variables (DBHOST, DBUSER, etc.)
   ├── Configure Microsoft Graph API credentials
   ├── Set up OAuth2 applications in Azure AD
   ├── Configure AI service API keys (OpenAI/Azure)
   └── Set up email notification templates

4. Testing
   ├── Run PHPUnit tests: phpunit --testdox
   ├── Test email processing with sample emails
   ├── Verify OAuth2 authentication flow
   └── Test AI integration and categorization
```

### Deployment Architecture Options

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Deployment Patterns                                 │
└─────────────────────────────────────────────────────────────────────────────┘

Single Server Deployment:
┌─────────────────┐
│   Web Server    │ ──────► All components on one server
│ • PHP App       │         Suitable for small organizations
│ • Database      │         Easy to manage and maintain
│ • File Storage  │
└─────────────────┘

Multi-Tier Deployment:
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Tier      │    │  Application    │    │   Database      │
│ • Load Balancer │◄──►│     Tier        │◄──►│     Tier        │
│ • Web Servers   │    │ • PHP Servers   │    │ • MariaDB       │
│ • SSL Termination│    │ • File Storage  │    │ • Replication   │
└─────────────────┘    └─────────────────┘    └─────────────────┘

Cloud Deployment (Azure):
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Azure App     │    │   Azure SQL     │    │  Azure Storage  │
│    Service      │◄──►│   Database      │    │   Account       │
│ • Auto-scaling  │    │ • Managed       │    │ • File Storage  │
│ • SSL included  │    │ • Backup        │    │ • CDN           │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Performance Optimization

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     Performance Considerations                             │
└─────────────────────────────────────────────────────────────────────────────┘

Database Optimization:
├── Index optimization for frequent queries
├── Query optimization for ticket searches
├── Connection pooling for high concurrency
├── Read replicas for reporting queries
└── Partitioning for large mail tables

Application Optimization:
├── Smarty template compilation caching
├── OpCache for PHP bytecode caching
├── Redis/Memcached for session storage
├── CDN for static assets
└── Gzip compression for responses

Email Processing Optimization:
├── Batch processing for multiple emails
├── Asynchronous processing with queues
├── Rate limiting for Graph API calls
├── Caching for AI categorization results
└── Parallel processing for attachments

Monitoring and Logging:
├── Application performance monitoring
├── Database query performance tracking
├── Email processing metrics
├── AI service usage monitoring
└── Security event logging
```

### Maintenance and Operations

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      Operational Procedures                                │
└─────────────────────────────────────────────────────────────────────────────┘

Regular Maintenance:
├── Database backup and recovery procedures
├── Log rotation and cleanup
├── Certificate renewal automation
├── Security updates and patches
└── Performance monitoring and tuning

Troubleshooting Common Issues:
├── Email processing failures
│   └── Check Graph API credentials and permissions
├── Authentication problems
│   └── Verify OAuth2 configuration and certificates
├── Database connection issues
│   └── Check connection strings and firewall rules
├── AI service failures
│   └── Verify API keys and rate limits
└── File upload problems
    └── Check file permissions and storage limits

Backup Strategy:
├── Database: Daily automated backups with point-in-time recovery
├── Files: Regular backup of uploaded attachments and logs
├── Configuration: Version control for configuration files
└── Certificates: Secure backup of SSL and API certificates

Scaling Considerations:
├── Horizontal scaling with load balancers
├── Database read replicas for reporting
├── CDN for static content delivery
├── Queue systems for background processing
└── Microservices architecture for large deployments
```

## Prerequisites

Before installing EOTS, ensure you have:

- **Web Server**: Apache or Nginx with PHP 8.0+
- **Database**: MariaDB 10.4+ or MySQL 8.0+
- **PHP Extensions**: 
  - `curl`
  - `json`
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `xml`
- **Microsoft 365 Tenant**: With administrative access to create Azure applications
- **SSL Certificate**: Required for OAuth2 callbacks (can use Let's Encrypt)

## Azure Application Setup

EOTS requires **two separate Azure applications** to function properly:

### 1. Mail Fetching Application (Service Principal)

This application is used for server-to-server communication to fetch emails from mailboxes.

#### Steps to Create:
1. Go to [Azure Portal](https://portal.azure.com) → Azure Active Directory → App registrations
2. Click "New registration"
3. Configure:
   - **Name**: `EOTS Mail Service`
   - **Supported account types**: Accounts in this organizational directory only
   - **Redirect URI**: Leave empty for now
4. After creation, note down:
   - **Application (client) ID**
   - **Directory (tenant) ID**
5. Go to "Certificates & secrets" → "New client secret"
   - **Description**: `EOTS Mail Access`
   - **Expires**: Choose appropriate duration
   - **Copy the secret value** (you won't see it again!)
6. Go to "API permissions" → "Add a permission" → "Microsoft Graph" → "Application permissions"
7. Add these permissions:
   - `Mail.Read` (to read emails)
   - `Mail.ReadWrite` (to modify email subjects)
   - `User.Read.All` (to read user information)
8. Click "Grant admin consent" for your organization

#### Certificate Setup (Recommended):
1. Generate a self-signed certificate or use your organization's certificate
2. Go to "Certificates & secrets" → "Certificates" → "Upload certificate"
3. Upload your certificate (.cer, .pem, or .crt file)
4. Store the private key securely for application configuration

### 2. User Login Application (OAuth2)

This application handles user authentication and login.

#### Steps to Create:
1. Go to Azure Portal → Azure Active Directory → App registrations
2. Click "New registration"
3. Configure:
   - **Name**: `EOTS User Login`
   - **Supported account types**: Accounts in this organizational directory only
   - **Redirect URI**: 
     - Type: Web
     - URL: `https://yourdomain.com/oauth/callback.php`
4. After creation, note down:
   - **Application (client) ID**
   - **Directory (tenant) ID**
5. Go to "Certificates & secrets" → "New client secret"
   - **Description**: `EOTS User Login`
   - **Expires**: Choose appropriate duration
   - **Copy the secret value**
6. Go to "API permissions" → "Add a permission" → "Microsoft Graph" → "Delegated permissions"
7. Add these permissions:
   - `User.Read` (to read user profile)
   - `email` (to access email address)
   - `openid` (for OpenID Connect)
   - `profile` (to access user profile)
   - `offline_access` (for refresh tokens)
8. Go to "Authentication" and configure:
   - **Redirect URIs**: Add your callback URL
   - **Logout URL**: `https://yourdomain.com/logout.php`
   - **Implicit grant**: Enable "ID tokens"

## Installation

### 1. Download and Extract

```bash
# Clone the repository
git clone https://github.com/your-org/EOTS.git
cd EOTS

# Or download and extract the ZIP file
wget https://github.com/your-org/EOTS/archive/main.zip
unzip main.zip
cd EOTS-main
```

### 2. Set Up Web Server

#### Apache Configuration:
```apache
<VirtualHost *:443>
    ServerName EOTS.yourdomain.com
    DocumentRoot /var/www/EOTS

    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key

    <Directory /var/www/EOTS>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx Configuration:
```nginx
server {
    listen 443 ssl;
    server_name EOTS.yourdomain.com;
    root /var/www/EOTS;
    index index.php;

    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Database Setup

```sql
-- Create database
CREATE DATABASE EOTS CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Create user
CREATE USER 'EOTS'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON EOTS.* TO 'EOTS'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Run Installation

1. Navigate to `https://yourdomain.com/_install/`
2. Follow the web-based installation wizard
3. The installer will:
   - Check system requirements
   - Create database tables
   - Set up initial configuration
   - Create default categories and statuses

### 5. Remove Installation Directory

After successful installation:
```bash
rm -rf _install/
```

## Configuration

### Database Configuration

Edit your database connection settings in `src/bootstrap.php` or create a configuration file:

```php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'EOTS');
define('DB_USER', 'EOTS');
define('DB_PASS', 'your_secure_password');
```

### Essential Configuration Values

After installation, configure these values in the admin panel or directly in the database:

#### Azure/Microsoft Integration
```sql
-- Tenant ID (same for both applications)
UPDATE Config SET Value = 'your-tenant-id' WHERE Name = 'tenantId';

-- Mail Fetching Application
UPDATE Config SET Value = 'mail-app-client-id' WHERE Name = 'application.clientId';
UPDATE Config SET Value = 'mail-app-client-secret' WHERE Name = 'application.clientSecret';

-- User Login Application  
UPDATE Config SET Value = 'login-app-client-id' WHERE Name = 'user.clientId';
UPDATE Config SET Value = 'login-app-client-secret' WHERE Name = 'user.clientSecret';
UPDATE Config SET Value = 'https://yourdomain.com/oauth/callback.php' WHERE Name = 'user.redirectUri';
```

#### Mail Configuration
```sql
-- Source mailbox for ticket creation
UPDATE Config SET Value = 'support@yourdomain.com' WHERE Name = 'source.mailbox';

-- Site configuration
UPDATE Config SET Value = 'Your Organization Ticket System' WHERE Name = 'site.title';
UPDATE Config SET Value = 'yourdomain.com' WHERE Name = 'site.domain';
```

#### Certificate Configuration (if using certificates)
```sql
-- Upload your certificate and private key
UPDATE Config SET Value = '-----BEGIN CERTIFICATE-----\n...\n-----END CERTIFICATE-----' WHERE Name = 'application.certificate';
UPDATE Config SET Value = '-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----' WHERE Name = 'application.certificateKey';
```

### Optional AI Configuration

If you want to enable AI features:

```sql
-- OpenAI Configuration
UPDATE Config SET Value = 'your-openai-api-key' WHERE Name = 'ai.openai.api.secret';
UPDATE Config SET Value = 'gpt-4' WHERE Name = 'ai.openai.model';

-- Azure AI Configuration (alternative to OpenAI)
UPDATE Config SET Value = 'your-azure-endpoint' WHERE Name = 'ai.azure.model.endpoint';
UPDATE Config SET Value = 'your-azure-api-key' WHERE Name = 'ai.azure.api.key';
```

### Email Processing Setup

To automatically process emails, set up a cron job:

```bash
# Add to crontab (crontab -e)
# Process emails every 5 minutes
*/5 * * * * /usr/bin/php /var/www/EOTS/_script/process_emails.php

# Daily cleanup and maintenance
0 2 * * * /usr/bin/php /var/www/EOTS/_script/daily_maintenance.php
```

## Usage

### For End Users

1. **Login**: Navigate to your EOTS URL and click "Login with Microsoft"
2. **View Tickets**: See all your tickets on the dashboard
3. **Create Tickets**: Send emails to the configured support address
4. **Track Progress**: Monitor ticket status and receive email notifications

### For Support Agents

1. **Agent Dashboard**: Access the agent interface to see all open tickets
2. **Ticket Management**: 
   - View ticket details and history
   - Add comments and updates
   - Change ticket status and priority
   - Assign tickets to other agents
3. **Email Integration**: Responses are automatically sent to users
4. **Reporting**: Generate reports on ticket metrics

### For Administrators

1. **System Configuration**: Manage system settings and integrations
2. **User Management**: Control user access and permissions
3. **Category Management**: Create and manage ticket categories
4. **Reporting**: Access comprehensive system reports
5. **AI Configuration**: Set up and configure AI features

## API Documentation

EOTS provides RESTful APIs for integration:

### Authentication
All API requests require authentication via Bearer token or API key.

### Endpoints

#### Tickets
- `GET /api/tickets` - List tickets
- `GET /api/tickets/{id}` - Get ticket details
- `POST /api/tickets` - Create new ticket
- `PUT /api/tickets/{id}` - Update ticket
- `DELETE /api/tickets/{id}` - Delete ticket

#### Users
- `GET /api/users` - List users
- `GET /api/users/{id}` - Get user details
- `POST /api/users` - Create user

#### Categories
- `GET /api/categories` - List categories
- `POST /api/categories` - Create category

For detailed API documentation, visit `/api/docs` after installation.

## Troubleshooting

### Common Issues

#### 1. Authentication Errors
- **Problem**: "Invalid client" or "Unauthorized" errors
- **Solution**: 
  - Verify Azure application client IDs and secrets
  - Check redirect URIs match exactly
  - Ensure proper API permissions are granted

#### 2. Email Fetching Issues
- **Problem**: Emails not being processed
- **Solution**:
  - Check mail application permissions
  - Verify mailbox configuration
  - Review cron job setup
  - Check application logs

#### 3. Database Connection Errors
- **Problem**: Cannot connect to database
- **Solution**:
  - Verify database credentials
  - Check database server status
  - Ensure proper PHP extensions are installed

#### 4. SSL/Certificate Issues
- **Problem**: OAuth callback fails
- **Solution**:
  - Ensure SSL certificate is valid
  - Check redirect URI configuration
  - Verify HTTPS is properly configured

### Log Files

Check these locations for troubleshooting:
- Application logs: `/var/www/EOTS/log/`
- Web server logs: `/var/log/apache2/` or `/var/log/nginx/`
- PHP logs: `/var/log/php/`

### Getting Help

1. Check the troubleshooting section above
2. Review log files for error messages
3. Verify Azure application configuration
4. Test database connectivity
5. Contact your system administrator

## Contributing

We welcome contributions to EOTS! Please follow these guidelines:

### Development Setup

1. Fork the repository
2. Create a development branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Set up a local development environment
4. Make your changes and test thoroughly
5. Submit a pull request

### Code Standards

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Include unit tests for new features
- Update documentation as needed

### Reporting Issues

When reporting issues, please include:
- Detailed description of the problem
- Steps to reproduce
- Expected vs actual behavior
- System information (PHP version, database version, etc.)
- Relevant log entries

## License

This project is licensed under the [MIT License](LICENSE).

### Third-Party Components

EOTS includes several third-party components:
- **Smarty Template Engine**: Licensed under LGPL
- **PHPMailer**: Licensed under LGPL
- **Various JavaScript libraries**: See individual license files

For complete license information, see the `LICENSE` file and individual component licenses in the `src/Vendor/` directory.
