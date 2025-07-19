<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

if (!isset($_POST["topic"]))
	die(jsonStatus(false, "Topic not found."));

$topic = $_POST["topic"];
if (strlen($topic) < 25)
	die(jsonStatus(false, "Topic too short."));

$prompt = PromptHelper::generatePropmptForArticleGeneration($topic);
$content = AiService::getRepsonse($prompt);

if (!$content)
	die(jsonStatus(false, "OpenAI response not valid."));

die(jsonStatus(true, "", ["content" => $content]));
