<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Validate required parameters
if (!isset($_POST['title'])) {
	echo jsonStatus(false, "Missing required parameters 1");
}

try {
	$article = new Article(0);
	$article->Title = $_POST['title'];
	$article->Slug = slugify($article->Title . "_" . uniqid());
	$article->AccessLevel = "Public";

	// Save the article
	$newArticle = ArticleController::save($article);

	echo jsonStatus(true, "", ["article" => $newArticle->toJsonObject()]);
} catch (Exception $e) {
	echo jsonStatus(false);
}
