<?php
require_once __DIR__ . '/src/bootstrap.php';

// Set headers for ICO and caching
header('Content-Type: image/x-icon');
header('Cache-Control: public, max-age=86400');

// Get favicon data from config and decode
$faviconData = Config::get('favicon.data');
if ($faviconData) {
	echo base64_decode($faviconData);
} else {
	// Output a 1x1 transparent ICO if no favicon is configured
	echo base64_decode('AAABAAEAAQEAAAEAIAAwAAAAFgAAACgAAAABAAAAAgAAAAEAIAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAAA==');
}

