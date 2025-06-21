<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAdmin();

header('Content-Type: application/json');

$r = [];
$isEditable = isset($_GET['format']) && $_GET['format'] === 'editable';

foreach (NotificationTemplateController::getAll() as $template) {
	if ($isEditable) {
		$r[] = [
			'value' => $template->getNotificationTemplateId(),
			'text' => $template->getName(),
		];
	} else {
		$r[] = $template->toJsonObject();
	}
}
echo json_encode($r);
