<?php
require_once __DIR__ . '/../../src/bootstrap.php';

// Ensure user is logged in as agent
Login::requireIsAgent();

// Check if files were uploaded
if (empty($_FILES['file'])) {
	echo jsonStatus(false, "No file uploaded");
	exit;
}

try {
	// Validate file upload
	if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
		throw new Exception("File upload failed");
	}

	if (!isset($_POST['menuitem'])) {
		throw new Exception("Menu item ID is required");
	}

	$newFile = FileHelper::createFileFromUpload($_FILES['file']);

	// Update menu item with new file ID
	$menuItem = new MenuItem($_POST['menuitem']);
	if (!$menuItem->isValid()) {
		throw new Exception("Invalid menu item");
	}

	$menuItem->update("ImageFileId", $newFile->getFileIdAsInt());
	$menuItem->spawn();

	echo jsonStatus(true, "File uploaded successfully", ["menuitem" => $menuItem->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false, $e->getMessage());
}
