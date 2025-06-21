<?php
require_once __DIR__ . '/src/bootstrap.php';

if (!login::isLoggedIn())
	login::bringToLogin();
else
	login::bringtToDashboard();

