<?php

// Import base traits first
require_once __DIR__ . '/Base/_import.php';

// Static list of trait files for better performance (no filesystem scanning)
$traitFiles = [
	'ActionGroupTrait.class.php',
	'ActionItemTrait.class.php',
	'AiCacheTrait.class.php',
	'ArticleTrait.class.php',
	'CategorySuggestionTrait.class.php',
	'CategoryTrait.class.php',
	'ConfigTrait.class.php',
	'FileTrait.class.php',
	'LogMailSentTrait.class.php',
	'MailAttachmentIgnoreTrait.class.php',
	'MailAttachmentTrait.class.php',
	'MailTrait.class.php',
	'MenuItemTrait.class.php',
	'MenuTrait.class.php',
	'NotificationTemplateTrait.class.php',
	'OrganizationUserTrait.class.php',
	'StatusTrait.class.php',
	'TemplateTextTrait.class.php',
	'TextReplaceTrait.class.php',
	'TicketActionItemTrait.class.php',
	'TicketAssociateTrait.class.php',
	'TicketCommentTrait.class.php',
	'TicketFileTrait.class.php',
	'TicketStatusTrait.class.php',
	'TicketTrait.class.php',
	'UserImageTrait.class.php',
	'UserTrait.class.php'
];

// Load trait files efficiently
foreach ($traitFiles as $file) {
	require_once __DIR__ . '/' . $file;
}
