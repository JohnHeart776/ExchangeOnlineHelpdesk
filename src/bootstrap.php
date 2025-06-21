<?php

// Error reporting configuration
const ERROR_MAIL_TO = 'christopher.pfaffinger@nv.at';
if (PHP_SAPI === 'cli') {
	define('ERROR_REPORTING_TO_MAIL_ENABLED', true);;
} else { //debug.reporting.error.mail.recipient
	define('ERROR_REPORTING_TO_MAIL_ENABLED', false);
}


// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
	if (!(error_reporting() & $errno)) {
		return false;
	}

	$stack_trace = print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true);
	$timestamp = date('Y-m-d H:i:s');

	$html_template = '
		<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
			<h2 style="color: #d32f2f; margin-top: 0;">Error Report</h2>
			<div style="background-color: #fff; padding: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.12);">
				<p><strong>Error Type:</strong> [%s] %s</p>
				<p><strong>File:</strong> %s</p>
				<p><strong>Line:</strong> %d</p>
				<p><strong>Time:</strong> %s</p>
				<div style="margin-top: 20px;">
					<h3 style="color: #666;">Stack Trace:</h3>
					<pre style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">%s</pre>
				</div>
			</div>
		</div>';

	$formatted_error = sprintf(
		$html_template,
		$errno,
		$errstr,
		$errfile,
		$errline,
		$timestamp,
		htmlspecialchars($stack_trace)
	);

	$plain_error = sprintf(
		"Error: [%s] %s\nFile: %s\nLine: %d\nTime: %s\nStack trace:\n%s",
		$errno,
		$errstr,
		$errfile,
		$errline,
		$timestamp,
		$stack_trace
	);

	error_log($plain_error);
	if (ERROR_REPORTING_TO_MAIL_ENABLED) {
		mail(ERROR_MAIL_TO, 'Application Error Report', $formatted_error,
			"MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n");

		if (ini_get('display_errors')) {
			echo PHP_SAPI === 'cli' ? $plain_error : $formatted_error;
		}
	}

	return true;
}

// Exception handler
function customExceptionHandler($exception)
{
	$timestamp = date('Y-m-d H:i:s');
	$stack_trace = $exception->getTraceAsString();

	$html_template = '
		<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
			<h2 style="color: #d32f2f; margin-top: 0;">Exception Report</h2>
			<div style="background-color: #fff; padding: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.12);">
				<p><strong>Exception Type:</strong> %s</p>
				<p><strong>Message:</strong> %s</p>
				<p><strong>File:</strong> %s</p>
				<p><strong>Line:</strong> %d</p>
				<p><strong>Time:</strong> %s</p>
				<div style="margin-top: 20px;">
					<h3 style="color: #666;">Stack Trace:</h3>
					<pre style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">%s</pre>
				</div>
			</div>
		</div>';

	$formatted_error = sprintf(
		$html_template,
		get_class($exception),
		$exception->getMessage(),
		$exception->getFile(),
		$exception->getLine(),
		$timestamp,
		htmlspecialchars($stack_trace)
	);

	$plain_error = sprintf(
		"Exception: [%s] %s\nFile: %s\nLine: %d\nTime: %s\nStack trace:\n%s",
		get_class($exception),
		$exception->getMessage(),
		$exception->getFile(),
		$exception->getLine(),
		$timestamp,
		$stack_trace
	);

	error_log($plain_error);
	if (ERROR_REPORTING_TO_MAIL_ENABLED) {
		mail(ERROR_MAIL_TO, 'Application Exception Report', $formatted_error,
			"MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n");
	}

	if (ini_get('display_errors')) {
		echo PHP_SAPI === 'cli' ? $plain_error : $formatted_error;
	}
}

// Register handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Register shutdown function
register_shutdown_function(function () {
	$error = error_get_last();
	if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
		$error_message = sprintf(
			"Fatal Error: [%s] %s\nFile: %s\nLine: %d\nTime: %s",
			$error['type'],
			$error['message'],
			$error['file'],
			$error['line'],
			date('Y-m-d H:i:s')
		);

		if (ERROR_REPORTING_ENABLED) {
			error_log($error_message);
			mail(ERROR_MAIL_TO, 'Application Fatal Error Report', $error_message);
		}
	}
});



//global functions
require_once __DIR__ . "/functions.php";

require_once __DIR__ . "/Database/_import.php";
require_once __DIR__ . "/Core/_import.php";
require_once __DIR__ . "/Auth/_import.php";
require_once __DIR__ . "/Client/_import.php";
require_once __DIR__ . "/Reporting/_import.php";

require_once __DIR__ . "/Trait/_import.php";
require_once __DIR__ . "/Application/_import.php";
require_once __DIR__ . "/Controller/_import.php";
require_once __DIR__ . "/Struct/_import.php";



// vendor libs
require_once __DIR__ . '/Vendor/kint/kint.phar';

require_once __DIR__ . '/Vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/Vendor/phpmailer/src/OAuth.php';
require_once __DIR__ . '/Vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/Vendor/phpmailer/src/PHPMailer.php';

require_once __DIR__ . '/Vendor/smarty/smarty-4.3.0/libs/Smarty.class.php';


//bring up database
global $d;
$d = \Database\Database::getInstance();

//bring up session
\Login::initSession();


//init smarty
global $s;
$s = new Smarty();
$s->setTemplateDir(__DIR__ . '/../smarty/templates/');
$s->setCompileDir(__DIR__ . '/../smarty/templates_c/');
$s->setCacheDir(__DIR__ . '/../smarty/cache/');
if (isTestServer())
	$s->force_compile = true;


