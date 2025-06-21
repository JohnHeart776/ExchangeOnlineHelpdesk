<?php
require_once __DIR__ . '/src/bootstrap.php';

// Set headers for SVG and caching
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=86400');

// Define supported logo variants
$supportedVariants = [
	'big-dark',
	'big-light',
	'big-light-color',
	'big-light-shade',
	'icon',
	'menu',
	'menu-dark',
	'wide-dark',
	'wide-light',
];

// Get requested variant from URL
$variant = $_GET['variant'] ?? 'icon';
if (!in_array($variant, $supportedVariants)) {
	$variant = 'icon';
}

// Get logo content from config
$logoContent = Config::get('logo.' . $variant);

// Output logo content or fallback
if ($logoContent) {
	echo $logoContent;
} else {
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#ccc"/></svg>';
}

