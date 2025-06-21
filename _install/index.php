<?php
/**
 * EOTS Installation Script
 * 
 * This script handles the initial setup of the EOTS system.
 * It performs the following steps:
 * 1. Check database environment variables
 * 2. Initialize database schema
 * 3. Populate configuration data
 * 4. Insert Category, Status, and Menu data
 */

// Prevent direct access if already installed
function isAlreadyInstalled() {
    try {
        require_once __DIR__ . '/../src/bootstrap.php';
        $db = \Database\Database::getInstance();
        $result = $db->getPDO("SELECT Value FROM Config WHERE Name = 'system.installed'", [], true);
        return $result && $result['Value'] == '1';
    } catch (Exception $e) {
        return false; // If we can't check, assume not installed
    }
}

if (isAlreadyInstalled()) {
    die('<h1>Installation Error</h1><p>The system is already installed. Installation cannot be run again.</p>');
}

// Handle form submission
$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$errors = [];
$success = [];

// Step 1: Check database environment variables
function checkDatabaseEnvironment() {
    $required_vars = ['DBHOST', 'DBUSER', 'DBPASSWORD', 'DBNAME'];
    $missing = [];

    foreach ($required_vars as $var) {
        if (!getenv($var)) {
            $missing[] = $var;
        }
    }

    return $missing;
}

// Step 2: Initialize database schema
function initializeSchema() {
    try {
        $schema_file = __DIR__ . '/schema/schema.sql';
        if (!file_exists($schema_file)) {
            throw new Exception("Schema file not found: $schema_file");
        }

        $sql = file_get_contents($schema_file);
        if ($sql === false) {
            throw new Exception("Could not read schema file");
        }

        // Remove MySQL dump specific commands that might cause issues
        $sql = preg_replace('/\/\*M!.*?\*\//', '', $sql);
        $sql = preg_replace('/\/\*!.*?\*\//', '', $sql);

        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        require_once __DIR__ . '/../src/Database/_import.php';
        $db = \Database\Database::getInstance();

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                $db->query($statement);
            }
        }

        return true;
    } catch (Exception $e) {
        throw new Exception("Schema initialization failed: " . $e->getMessage());
    }
}

// Step 3: Populate configuration data
function populateConfigData($config_values = []) {
    try {
        require_once __DIR__ . '/../src/Database/_import.php';
        $db = \Database\Database::getInstance();

        // Get default config values from file
        $default_configs = getConfigValuesFromFile();

        // Merge with user-provided values
        foreach ($default_configs as $config) {
            $name = $config['name'];
            $value = isset($config_values[$name]) ? $config_values[$name] : $config['value'];

            // Insert config value
            $stmt = $db->getPDOConnection()->prepare("INSERT INTO `Config` (Guid, Name, Value) VALUES (UUID(), ?, ?)");
            $stmt->execute([$name, $value]);
        }

        return true;
    } catch (Exception $e) {
        throw new Exception("Config population failed: " . $e->getMessage());
    }
}

// Get config values from SQL file with descriptions
function getConfigValuesFromFile() {
    $configs = [];
    $config_file = __DIR__ . '/schema/config.sql';

    if (file_exists($config_file)) {
        $sql = file_get_contents($config_file);
        preg_match('/INSERT INTO `Config` VALUES\s*(.*?);/s', $sql, $matches);

        if (isset($matches[1])) {
            // Parse config entries
            preg_match_all('/\(\d+,\'[^\']+\',\'([^\']+)\',\'([^\']*)\'\)/', $matches[1], $config_matches, PREG_SET_ORDER);

            foreach ($config_matches as $match) {
                $name = $match[1];
                $value = $match[2];

                $configs[] = [
                    'name' => $name,
                    'value' => $value,
                    'description' => getConfigDescription($name),
                    'format' => getConfigFormat($name),
                    'required' => isConfigRequired($name),
                    'category' => getConfigCategory($name)
                ];
            }
        }
    }

    return $configs;
}

// Get description for config value
function getConfigDescription($name) {
    $descriptions = [
        'tenantId' => 'Azure AD Tenant ID for Microsoft authentication',
        'application.clientId' => 'Azure AD Application Client ID for service authentication',
        'application.clientSecret' => 'Azure AD Application Client Secret for service authentication',
        'application.certificate' => 'X.509 Certificate for Azure AD application authentication (PEM format)',
        'application.certificateKey' => 'Private key for the X.509 certificate (PEM format)',
        'application.certificateKeyPassword' => 'Password for the certificate private key (if encrypted)',
        'source.mailbox' => 'Email address of the mailbox to monitor for incoming tickets',
        'source.mailCount' => 'Maximum number of emails to fetch from Microsoft Graph API per run',
        'source.mailbox.suffixSubject' => 'Whether to add ticket number suffix to email subjects',
        'user.clientId' => 'Azure AD Client ID for user authentication',
        'user.clientSecret' => 'Azure AD Client Secret for user authentication',
        'user.redirectUri' => 'OAuth redirect URI for user authentication callback',
        'user.oauthScopes' => 'OAuth scopes requested for user authentication',
        'user.authUrl' => 'Azure AD authorization endpoint URL',
        'user.tokenUrl' => 'Azure AD token endpoint URL',
        'site.title' => 'Title displayed in browser tab and application header',
        'site.domain' => 'Domain name of the application (used for links and redirects)',
        'site.name' => 'Short name of the application',
        'log.dir' => 'Directory path where log files are stored',
        'log.retention' => 'Number of days to keep log files before deletion',
        'sla.business.hours.from' => 'Start hour of business hours (24-hour format)',
        'sla.business.hours.to' => 'End hour of business hours (24-hour format)',
        'sla.business.days.from' => 'First business day of week (1=Monday, 7=Sunday)',
        'sla.business.days.to' => 'Last business day of week (1=Monday, 7=Sunday)',
        'sla.reaction.interval' => 'SLA reaction time interval (ISO 8601 duration format)',
        'ai.enable' => 'Enable or disable AI features throughout the application',
        'ai.vendor' => 'AI vendor to use (openai or azure)',
        'ai.openai.api.secret' => 'OpenAI API key for AI features',
        'ai.openai.model' => 'OpenAI model to use (e.g., gpt-4, gpt-3.5-turbo)',
        'ai.openai.proxy.enable' => 'Enable proxy for OpenAI API requests',
        'ai.openai.proxy.url' => 'Proxy URL for OpenAI API requests',
        'ai.azure.model.endpoint' => 'Azure OpenAI service endpoint URL',
        'ai.azure.api.key' => 'Azure OpenAI API key',
        'ai.azure.api.version' => 'Azure OpenAI API version',
        'ai.azure.model.deployment' => 'Azure OpenAI model deployment name',
        'ai.azure.api.tokens.max' => 'Maximum tokens for Azure OpenAI requests',
        'ai.azure.api.temperature' => 'Temperature setting for AI responses (0.0-1.0)',
        'ai.azure.cache.enabled' => 'Enable caching for AI responses',
        'mail.template' => 'HTML template for outgoing emails',
        'mail.logo.height' => 'Height of logo in email templates (pixels)',
        'mail.template.start' => 'HTML template for start of ticket update emails',
        'mail.template.end' => 'HTML template for end of ticket update emails',
        'debug.mails.route.all.enabled' => 'Route all outgoing emails to debug recipient',
        'debug.mails.route.all.to' => 'Email address to receive all debug emails',
        'debug.resetBeforeImport' => 'Reset data before importing emails (development only)',
        'debug.reporting.error.mail.web.enabled' => 'Send error reports via email for web errors',
        'debug.reporting.error.mail.cli.enabled' => 'Send error reports via email for CLI errors',
        'debug.reporting.error.mail.recipient' => 'Email address to receive error reports',
        'search.user.limit' => 'Maximum number of users to return in search results',
        'search.orguser.limit' => 'Maximum number of organization users to return in search',
        'search.ticket.limit' => 'Maximum number of tickets to return in search results',
        'search.attachment.limit' => 'Maximum number of attachments to return in search',
        'inscriptis.server' => 'Server hostname for Inscriptis text conversion service',
        'inscriptis.enable' => 'Enable Inscriptis service for HTML to text conversion',
        'gotenberg.server' => 'Server hostname for Gotenberg PDF generation service',
        'gotenberg.enable' => 'Enable Gotenberg service for PDF generation',
        'authentication.newuser.accesslevel.default' => 'Default access level for new users',
        'status.duplicate.internalName' => 'Internal name of the duplicate status',
        'noun.api.key' => 'API key for Noun Project icon service',
        'noun.api.secret' => 'API secret for Noun Project icon service',
        'iconfinder.api.key' => 'API key for IconFinder icon service',
        'iconfinder.api.clientid' => 'Client ID for IconFinder icon service',
        'text.login' => 'HTML content displayed on login page',
        'text.logout' => 'HTML content displayed on logout page'
    ];

    return isset($descriptions[$name]) ? $descriptions[$name] : 'Configuration value for ' . $name;
}

// Get format information for config value
function getConfigFormat($name) {
    $formats = [
        'tenantId' => 'UUID format (e.g., 12345678-1234-1234-1234-123456789abc)',
        'application.clientId' => 'UUID format (e.g., 12345678-1234-1234-1234-123456789abc)',
        'application.clientSecret' => 'Base64-encoded string from Azure AD',
        'application.certificate' => 'PEM format certificate (-----BEGIN CERTIFICATE-----...-----END CERTIFICATE-----)',
        'application.certificateKey' => 'PEM format private key (-----BEGIN PRIVATE KEY-----...-----END PRIVATE KEY-----)',
        'source.mailbox' => 'Valid email address (e.g., tickets@company.com)',
        'source.mailCount' => 'Positive integer (e.g., 1000)',
        'source.mailbox.suffixSubject' => 'Boolean: true or false',
        'user.clientId' => 'UUID format (e.g., 12345678-1234-1234-1234-123456789abc)',
        'user.clientSecret' => 'Base64-encoded string from Azure AD',
        'user.redirectUri' => 'Full URL (e.g., https://yourdomain.com/oauth/callback.php)',
        'user.oauthScopes' => 'Space-separated scopes (e.g., User.Read offline_access openid profile email)',
        'user.authUrl' => 'Full URL to Azure AD authorize endpoint',
        'user.tokenUrl' => 'Full URL to Azure AD token endpoint',
        'site.title' => 'Text string (e.g., My Ticket System)',
        'site.domain' => 'Domain name without protocol (e.g., tickets.company.com)',
        'site.name' => 'Short text string (e.g., Tickets)',
        'log.dir' => 'Absolute file system path (e.g., /var/www/html/log)',
        'log.retention' => 'Positive integer (days)',
        'sla.business.hours.from' => 'Hour in 24-hour format (0-23)',
        'sla.business.hours.to' => 'Hour in 24-hour format (0-23)',
        'sla.business.days.from' => 'Day of week (1=Monday, 7=Sunday)',
        'sla.business.days.to' => 'Day of week (1=Monday, 7=Sunday)',
        'sla.reaction.interval' => 'ISO 8601 duration (e.g., PT2H for 2 hours, P1D for 1 day)',
        'ai.enable' => 'Boolean: true or false',
        'ai.vendor' => 'Text: openai or azure',
        'ai.openai.api.secret' => 'OpenAI API key starting with sk-',
        'ai.openai.model' => 'Model name (e.g., gpt-4, gpt-3.5-turbo)',
        'ai.openai.proxy.enable' => 'Boolean: true or false',
        'ai.openai.proxy.url' => 'Full URL (e.g., https://proxy.example.com/openai.php)',
        'ai.azure.model.endpoint' => 'Full URL to Azure OpenAI endpoint',
        'ai.azure.api.key' => 'Azure OpenAI API key',
        'ai.azure.api.version' => 'API version (e.g., 2024-12-01-preview)',
        'ai.azure.model.deployment' => 'Deployment name from Azure OpenAI',
        'ai.azure.api.tokens.max' => 'Positive integer (e.g., 1500)',
        'ai.azure.api.temperature' => 'Decimal between 0.0 and 1.0 (e.g., 0.5)',
        'ai.azure.cache.enabled' => 'Boolean: true or false',
        'mail.logo.height' => 'Positive integer (pixels)',
        'debug.mails.route.all.enabled' => 'Boolean: true or false',
        'debug.mails.route.all.to' => 'Valid email address',
        'debug.resetBeforeImport' => 'Boolean: true or false',
        'debug.reporting.error.mail.web.enabled' => 'Boolean: true or false',
        'debug.reporting.error.mail.cli.enabled' => 'Boolean: true or false',
        'debug.reporting.error.mail.recipient' => 'Valid email address',
        'search.user.limit' => 'Positive integer (e.g., 10)',
        'search.orguser.limit' => 'Positive integer (e.g., 10)',
        'search.ticket.limit' => 'Positive integer (e.g., 25)',
        'search.attachment.limit' => 'Positive integer (e.g., 10)',
        'inscriptis.server' => 'Hostname or IP address',
        'inscriptis.enable' => 'Boolean: true or false',
        'gotenberg.server' => 'Hostname or IP address',
        'gotenberg.enable' => 'Boolean: true or false',
        'authentication.newuser.accesslevel.default' => 'Text: guest, user, agent, or admin',
        'status.duplicate.internalName' => 'Text string (internal status name)',
        'noun.api.key' => 'API key from Noun Project',
        'noun.api.secret' => 'API secret from Noun Project',
        'iconfinder.api.key' => 'API key from IconFinder',
        'iconfinder.api.clientid' => 'Client ID from IconFinder',
        'mail.template' => 'HTML content with placeholders',
        'mail.template.start' => 'HTML content with placeholders',
        'mail.template.end' => 'HTML content with placeholders',
        'text.login' => 'HTML content',
        'text.logout' => 'HTML content'
    ];

    return isset($formats[$name]) ? $formats[$name] : 'Text value';
}

// Check if config value is required
function isConfigRequired($name) {
    $required = [
        'tenantId', 'application.clientId', 'application.clientSecret',
        'source.mailbox', 'user.clientId', 'user.clientSecret',
        'user.redirectUri', 'user.authUrl', 'user.tokenUrl',
        'site.title', 'site.domain'
    ];

    return in_array($name, $required);
}

// Get config category
function getConfigCategory($name) {
    if (strpos($name, 'application.') === 0) return 'Azure Application Authentication';
    if (strpos($name, 'user.') === 0) return 'User Authentication';
    if (strpos($name, 'source.') === 0) return 'Email Source';
    if (strpos($name, 'site.') === 0) return 'Site Settings';
    if (strpos($name, 'ai.') === 0) return 'AI Configuration';
    if (strpos($name, 'mail.') === 0) return 'Email Templates';
    if (strpos($name, 'sla.') === 0) return 'SLA Settings';
    if (strpos($name, 'debug.') === 0) return 'Debug Settings';
    if (strpos($name, 'search.') === 0) return 'Search Limits';
    if (strpos($name, 'log.') === 0) return 'Logging';
    if (strpos($name, 'authentication.') === 0) return 'Authentication';
    if (strpos($name, 'text.') === 0) return 'UI Text';
    return 'Other';
}

// Step 4: Insert Category, Status, and Menu data
function insertInitialData($selected_categories, $selected_statuses) {
    try {
        require_once __DIR__ . '/../src/Database/_import.php';
        $db = \Database\Database::getInstance();

        // Insert Menu data
        $menu_file = __DIR__ . '/schema/menu.sql';
        if (file_exists($menu_file)) {
            $sql = file_get_contents($menu_file);
            preg_match('/INSERT INTO `Menu` VALUES\s*(.*?);/s', $sql, $matches);
            if (isset($matches[1])) {
                $insert_sql = "INSERT INTO `Menu` VALUES " . $matches[1];
                $db->query($insert_sql);
            }
        }

        // Insert selected Categories
        if (!empty($selected_categories)) {
            $category_file = __DIR__ . '/schema/category.sql';
            $sql = file_get_contents($category_file);
            preg_match('/INSERT INTO `Category` VALUES\s*(.*?);/s', $sql, $matches);
            if (isset($matches[1])) {
                // Parse individual category records and extract IDs
                $categories_data = $matches[1];
                preg_match_all('/\((\d+),[^)]+\)/', $categories_data, $category_matches, PREG_SET_ORDER);

                foreach ($category_matches as $match) {
                    $category_id = (int)$match[1];
                    if (in_array($category_id, $selected_categories)) {
                        $insert_sql = "INSERT INTO `Category` VALUES " . $match[0];
                        $db->query($insert_sql);
                    }
                }
            }
        }

        // Insert selected Statuses
        if (!empty($selected_statuses)) {
            $status_file = __DIR__ . '/schema/status.sql';
            $sql = file_get_contents($status_file);
            preg_match('/INSERT INTO `Status` VALUES\s*(.*?);/s', $sql, $matches);
            if (isset($matches[1])) {
                // Parse individual status records and extract IDs
                $statuses_data = $matches[1];
                preg_match_all('/\((\d+),[^)]+\)/', $statuses_data, $status_matches, PREG_SET_ORDER);

                foreach ($status_matches as $match) {
                    $status_id = (int)$match[1];
                    if (in_array($status_id, $selected_statuses)) {
                        $insert_sql = "INSERT INTO `Status` VALUES " . $match[0];
                        $db->query($insert_sql);
                    }
                }
            }
        }

        // Mark system as installed
        $db->query("INSERT INTO `Config` (Guid, Name, Value) VALUES (UUID(), 'system.installed', '1')");

        return true;
    } catch (Exception $e) {
        throw new Exception("Initial data insertion failed: " . $e->getMessage());
    }
}

// Parse category and status data for form display
function getCategoriesFromFile() {
    // Simplified approach - return hardcoded list based on the SQL file
    return [
        ['id' => 1, 'internal_name' => 'application', 'public_name' => 'Anwenderprogramme', 'icon' => 'fa-window-restore', 'color' => 'primary', 'is_default' => 0],
        ['id' => 2, 'internal_name' => 'hardware', 'public_name' => 'Hardware', 'icon' => 'fa-desktop', 'color' => 'dark', 'is_default' => 0],
        ['id' => 3, 'internal_name' => 'network', 'public_name' => 'Netzwerk', 'icon' => 'fa-network-wired', 'color' => 'info', 'is_default' => 0],
        ['id' => 4, 'internal_name' => 'server', 'public_name' => 'Server & Infrastruktur', 'icon' => 'fa-server', 'color' => 'dark', 'is_default' => 0],
        ['id' => 5, 'internal_name' => 'security', 'public_name' => 'Sicherheit', 'icon' => 'fa-shield-halved', 'color' => 'primary', 'is_default' => 0],
        ['id' => 6, 'internal_name' => 'access', 'public_name' => 'Berechtigungen & Zugriffe', 'icon' => 'fa-key', 'color' => 'warning', 'is_default' => 0],
        ['id' => 7, 'internal_name' => 'email', 'public_name' => 'E-Mail & Kommunikation', 'icon' => 'fa-envelope', 'color' => 'success', 'is_default' => 0],
        ['id' => 8, 'internal_name' => 'cloud', 'public_name' => 'Cloud & SaaS', 'icon' => 'fa-cloud', 'color' => 'info', 'is_default' => 0],
        ['id' => 9, 'internal_name' => 'backup', 'public_name' => 'Backup & Wiederherstellung', 'icon' => 'fa-database', 'color' => 'dark', 'is_default' => 0],
        ['id' => 10, 'internal_name' => 'software_deployment', 'public_name' => 'Softwareverteilung', 'icon' => 'fa-download', 'color' => 'primary', 'is_default' => 0],
        ['id' => 11, 'internal_name' => 'user_account', 'public_name' => 'Benutzerkonten', 'icon' => 'fa-user', 'color' => 'primary', 'is_default' => 0],
        ['id' => 12, 'internal_name' => 'printing', 'public_name' => 'Drucken & Scannen', 'icon' => 'fa-print', 'color' => 'dark', 'is_default' => 0],
        ['id' => 13, 'internal_name' => 'telephony', 'public_name' => 'Telefonie & VoIP', 'icon' => 'fa-phone', 'color' => 'info', 'is_default' => 0],
        ['id' => 14, 'internal_name' => 'mobile', 'public_name' => 'Mobile Geräte', 'icon' => 'fa-mobile-screen', 'color' => 'dark', 'is_default' => 0],
        ['id' => 15, 'internal_name' => 'web', 'public_name' => 'Webanwendungen', 'icon' => 'fa-globe', 'color' => 'primary', 'is_default' => 0],
        ['id' => 16, 'internal_name' => 'erp', 'public_name' => 'ERP & Buchhaltung', 'icon' => 'fa-chart-line', 'color' => 'warning', 'is_default' => 0],
        ['id' => 17, 'internal_name' => 'reporting', 'public_name' => 'Reporting & BI', 'icon' => 'fa-chart-bar', 'color' => 'success', 'is_default' => 0],
        ['id' => 18, 'internal_name' => 'other', 'public_name' => 'Sonstiges', 'icon' => 'fa-star', 'color' => 'primary', 'is_default' => 0],
        ['id' => 19, 'internal_name' => 'default', 'public_name' => 'Allgemein', 'icon' => 'fa-circle-question', 'color' => 'primary', 'is_default' => 1],
        ['id' => 20, 'internal_name' => 'serverupdates_linux', 'public_name' => 'Serverupdates - Linux', 'icon' => 'fa-server', 'color' => 'danger', 'is_default' => 0],
        ['id' => 21, 'internal_name' => 'serverupdates_windows', 'public_name' => 'Serverupdates - Windows', 'icon' => 'fa-server', 'color' => 'danger', 'is_default' => 0],
        ['id' => 22, 'internal_name' => 'spam', 'public_name' => 'Spam', 'icon' => 'fa-spaghetti-monster-flying', 'color' => 'primary', 'is_default' => 0],
        ['id' => 23, 'internal_name' => 'security_incident', 'public_name' => 'Sicherheitsvorfall', 'icon' => '', 'color' => '', 'is_default' => 0],
        ['id' => 24, 'internal_name' => 'security_radius', 'public_name' => 'Security Radius', 'icon' => '', 'color' => '', 'is_default' => 0]
    ];
}

function getStatusesFromFile() {
    // Simplified approach - return hardcoded list based on the SQL file
    return [
        ['id' => 1, 'internal_name' => 'open', 'public_name' => 'Offen', 'color' => 'primary', 'icon' => 'fa-folder-open', 'is_default' => 1],
        ['id' => 3, 'internal_name' => 'assigned', 'public_name' => 'Zugewiesen', 'color' => 'info', 'icon' => 'fa-user-check', 'is_default' => 0],
        ['id' => 4, 'internal_name' => 'working', 'public_name' => 'In Bearbeitung', 'color' => 'success', 'icon' => 'fa-spinner fa-pulse', 'is_default' => 0],
        ['id' => 5, 'internal_name' => 'waiting_customer', 'public_name' => 'Warten auf Kunde', 'color' => 'warning', 'icon' => 'fa-user-clock', 'is_default' => 0],
        ['id' => 6, 'internal_name' => 'waiting_internal', 'public_name' => 'Warten intern', 'color' => 'warning', 'icon' => 'fa-users-gear', 'is_default' => 0],
        ['id' => 7, 'internal_name' => 'on_hold', 'public_name' => 'Zurückgestellt', 'color' => 'dark', 'icon' => 'fa-pause-circle', 'is_default' => 0],
        ['id' => 8, 'internal_name' => 'reply_customer', 'public_name' => 'Kundenantwort', 'color' => 'warning', 'icon' => 'fa-beat fa-reply', 'is_default' => 0],
        ['id' => 10, 'internal_name' => 'resolved', 'public_name' => 'Gelöst', 'color' => 'success', 'icon' => 'fa-circle-check', 'is_default' => 0],
        ['id' => 12, 'internal_name' => 'closed', 'public_name' => 'Geschlossen', 'color' => 'tertiary', 'icon' => 'fa-box-archive', 'is_default' => 0],
        ['id' => 13, 'internal_name' => 'reopened', 'public_name' => 'Wieder geöffnet', 'color' => 'primary', 'icon' => 'fa-redo', 'is_default' => 0],
        ['id' => 16, 'internal_name' => 'duplicate', 'public_name' => 'Duplikat', 'color' => 'tertiary', 'icon' => 'fa-copy', 'is_default' => 0],
        ['id' => 17, 'internal_name' => 'escalated', 'public_name' => 'Eskalation', 'color' => 'danger', 'icon' => 'fa-arrow-up-right-dots', 'is_default' => 0]
    ];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            $missing_vars = checkDatabaseEnvironment();
            if (empty($missing_vars)) {
                $step = 2;
                $success[] = "Database environment variables are properly configured.";
            } else {
                $errors[] = "Missing required database environment variables: " . implode(', ', $missing_vars);
            }
            break;

        case 2:
            try {
                initializeSchema();
                $step = 3;
                $success[] = "Database schema initialized successfully.";
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            break;

        case 3:
            $config_values = isset($_POST['config']) ? $_POST['config'] : [];
            try {
                populateConfigData($config_values);
                $step = 4;
                $success[] = "Configuration data populated successfully.";
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            break;

        case 4:
            $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];
            $selected_statuses = isset($_POST['statuses']) ? $_POST['statuses'] : [];

            try {
                insertInitialData($selected_categories, $selected_statuses);
                $step = 5; // Installation complete
                $success[] = "Installation completed successfully!";
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            break;

        case 5:
            // Handle post-installation actions
            if (isset($_POST['remove_installer']) && $_POST['remove_installer'] === 'yes') {
                try {
                    removeInstallerDirectory();
                    $success[] = "Installer directory removed successfully.";
                } catch (Exception $e) {
                    $errors[] = "Failed to remove installer directory: " . $e->getMessage();
                }
            }
            break;
    }
}

// Function to remove installer directory
function removeInstallerDirectory() {
    $installer_dir = __DIR__;

    // Recursive directory removal function
    function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }

    if (!removeDirectory($installer_dir)) {
        throw new Exception("Could not remove installer directory. Please remove it manually.");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketHarbor Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .step {
            background: #e3f2fd;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        .success {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .checkbox-group {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 3px;
        }
        .checkbox-item:hover {
            background-color: #f5f5f5;
        }
        .checkbox-item input {
            margin-right: 10px;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-name {
            font-weight: bold;
        }
        .item-description {
            font-size: 0.9em;
            color: #666;
        }
        .btn {
            background: #2196f3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #1976d2;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .progress {
            background: #e0e0e0;
            height: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .progress-bar {
            background: #2196f3;
            height: 100%;
            transition: width 0.3s ease;
        }
        .config-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .config-item label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .config-description {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f0f8ff;
            border-left: 3px solid #2196f3;
            border-radius: 3px;
        }
        .config-item input[type="text"],
        .config-item textarea {
            border: 1px solid #ddd;
            border-radius: 3px;
            font-family: monospace;
        }
        .config-item input[type="text"]:focus,
        .config-item textarea:focus {
            border-color: #2196f3;
            outline: none;
            box-shadow: 0 0 5px rgba(33, 150, 243, 0.3);
        }
        h3 {
            color: #2196f3;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>TicketHarbor Installation</h1>

        <div class="progress">
            <div class="progress-bar" style="width: <?= min(($step / 5) * 100, 100) ?>%"></div>
        </div>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <?php foreach ($success as $msg): ?>
                <div class="success"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <div class="step">Step 1: Database Environment Check</div>
            <p>This step verifies that all required database environment variables are set.</p>
            <p>Required variables: <strong>DBHOST, DBUSER, DBPASSWORD, DBNAME</strong></p>

            <?php
            $missing_vars = checkDatabaseEnvironment();
            if (!empty($missing_vars)):
            ?>
                <div class="error">
                    <strong>Missing environment variables:</strong><br>
                    <?php foreach ($missing_vars as $var): ?>
                        • <?= htmlspecialchars($var) ?><br>
                    <?php endforeach; ?>
                    <br>
                    Please set these environment variables before proceeding.
                </div>
            <?php else: ?>
                <div class="success">All required database environment variables are set.</div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="step" value="1">
                <button type="submit" class="btn" <?= !empty($missing_vars) ? 'disabled' : '' ?>>
                    Check Database Environment
                </button>
            </form>

        <?php elseif ($step == 2): ?>
            <div class="step">Step 2: Initialize Database Schema</div>
            <p>This step creates all necessary database tables and structures.</p>

            <form method="post">
                <input type="hidden" name="step" value="2">
                <button type="submit" class="btn">Initialize Database Schema</button>
            </form>

        <?php elseif ($step == 3): ?>
            <div class="step">Step 3: Configure Application Settings</div>
            <p>Configure the application settings. Required fields are marked with an asterisk (*). You can modify these values or use the defaults.</p>

            <form method="post">
                <input type="hidden" name="step" value="3">

                <?php
                $configs = getConfigValuesFromFile();
                $categories = [];

                // Group configs by category
                foreach ($configs as $config) {
                    $categories[$config['category']][] = $config;
                }

                foreach ($categories as $category => $categoryConfigs):
                ?>
                    <div class="form-group">
                        <h3><?= htmlspecialchars($category) ?></h3>

                        <?php foreach ($categoryConfigs as $config): ?>
                            <div class="config-item">
                                <label for="config_<?= htmlspecialchars($config['name']) ?>">
                                    <?= htmlspecialchars($config['name']) ?>
                                    <?php if ($config['required']): ?><span style="color: red;">*</span><?php endif; ?>
                                </label>

                                <div class="config-description">
                                    <strong>Description:</strong> <?= htmlspecialchars($config['description']) ?><br>
                                    <strong>Format:</strong> <?= htmlspecialchars($config['format']) ?>
                                </div>

                                <?php if (strlen($config['value']) > 100 || strpos($config['name'], 'template') !== false || strpos($config['name'], 'text.') === 0): ?>
                                    <textarea 
                                        name="config[<?= htmlspecialchars($config['name']) ?>]" 
                                        id="config_<?= htmlspecialchars($config['name']) ?>"
                                        rows="4"
                                        <?= $config['required'] ? 'required' : '' ?>
                                        style="width: 100%; margin-bottom: 10px;"
                                    ><?= htmlspecialchars($config['value']) ?></textarea>
                                <?php else: ?>
                                    <input 
                                        type="text" 
                                        name="config[<?= htmlspecialchars($config['name']) ?>]" 
                                        id="config_<?= htmlspecialchars($config['name']) ?>"
                                        value="<?= htmlspecialchars($config['value']) ?>"
                                        <?= $config['required'] ? 'required' : '' ?>
                                        style="width: 100%; margin-bottom: 10px; padding: 5px;"
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn">Save Configuration</button>
            </form>

        <?php elseif ($step == 4): ?>
            <div class="step">Step 4: Select Categories and Statuses</div>
            <p>Choose which categories and statuses to include in your installation. Default items are pre-selected.</p>

            <form method="post">
                <input type="hidden" name="step" value="4">

                <div class="form-group">
                    <label>Categories:</label>
                    <div class="checkbox-group">
                        <?php
                        $categories = getCategoriesFromFile();
                        foreach ($categories as $category):
                        ?>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       name="categories[]" 
                                       value="<?= $category['id'] ?>"
                                       id="cat_<?= $category['id'] ?>"
                                       <?= $category['is_default'] ? 'checked' : '' ?>>
                                <label for="cat_<?= $category['id'] ?>" class="item-details">
                                    <div class="item-name"><?= htmlspecialchars($category['public_name']) ?></div>
                                    <div class="item-description">
                                        Internal: <?= htmlspecialchars($category['internal_name']) ?>
                                        <?php if ($category['icon']): ?>
                                            | Icon: <?= htmlspecialchars($category['icon']) ?>
                                        <?php endif; ?>
                                        <?php if ($category['color']): ?>
                                            | Color: <?= htmlspecialchars($category['color']) ?>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Statuses:</label>
                    <div class="checkbox-group">
                        <?php
                        $statuses = getStatusesFromFile();
                        foreach ($statuses as $status):
                        ?>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       name="statuses[]" 
                                       value="<?= $status['id'] ?>"
                                       id="status_<?= $status['id'] ?>"
                                       <?= $status['is_default'] ? 'checked' : '' ?>>
                                <label for="status_<?= $status['id'] ?>" class="item-details">
                                    <div class="item-name"><?= htmlspecialchars($status['public_name']) ?></div>
                                    <div class="item-description">
                                        Internal: <?= htmlspecialchars($status['internal_name']) ?>
                                        <?php if ($status['color']): ?>
                                            | Color: <?= htmlspecialchars($status['color']) ?>
                                        <?php endif; ?>
                                        <?php if ($status['icon']): ?>
                                            | Icon: <?= htmlspecialchars($status['icon']) ?>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn">Complete Installation</button>
            </form>

        <?php elseif ($step == 5): ?>
            <div class="step">Installation Complete!</div>
            <div class="success">
                <h3>TicketHarbor has been successfully installed!</h3>
                <p>The system is now ready to use. You can:</p>
                <ul>
                    <li>Access the main application</li>
                    <li>Configure additional settings</li>
                    <li>Start using the ticket system</li>
                </ul>
                <p><strong>Note:</strong> This installer cannot be run again as the system is now marked as installed.</p>
            </div>

            <div class="form-group">
                <h3>Post-Installation Options</h3>
                <p>For security reasons, it's recommended to remove the installer directory after installation.</p>

                <form method="post" style="margin-bottom: 20px;">
                    <input type="hidden" name="step" value="5">
                    <input type="hidden" name="remove_installer" value="yes">
                    <button type="submit" class="btn" style="background-color: #f44336;" 
                            onclick="return confirm('Are you sure you want to remove the installer directory? This action cannot be undone.')">
                        Remove Installer Directory
                    </button>
                </form>
            </div>

            <a href="../index.php" class="btn">Go to Application</a>
        <?php endif; ?>
    </div>
</body>
</html>
