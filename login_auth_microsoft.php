<?php
require_once __DIR__ . '/src/bootstrap.php';

$authUrl = Config::getConfigValueFor('user.authUrl') .
	'?client_id=' . Config::getConfigValueFor('user.clientId')
	. '&response_type=code'
	. '&redirect_uri=' . urlencode(Config::getConfigValueFor('user.redirectUri'))
	. '&scope=' . urlencode(Config::getConfigValueFor('user.oauthScopes'));
// Leite den Benutzer zur Microsoft OAuth2-Seite weiter
header('Location: ' . $authUrl);
die();
