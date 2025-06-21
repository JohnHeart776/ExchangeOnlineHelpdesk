<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

// Check if ticket GUID is provided
if (!isset($_POST['guid']) || empty($_POST['guid'])) {
	die(jsonStatus(false, "No ticket GUID provided"));
}

// Check if AI is enabled in config
if (!Config::getConfigValueFor('ai.enable')) {
	die(jsonStatus(true, "ich kann keinen text generieren"));
}

try {
	// Get ticket by GUID
	$ticket = new Ticket($_POST['guid']);
	if (!$ticket) {
		die(jsonStatus(false, "Ticket not found"));
	}

	// Generate AI response
	$openAi = new OpenAiClient(
		authenticator: OpenAiApiAuthenticator::getDefault(),
		useLocalAiCache: true,
		appendBaselinePromptToPrompt: true,
	);

	$specific = "The Response should confirm the error the user was reporting. If the error is something a user can typically do by himself answer with the instructions how he can achieve his goal. If the question is more of an administrative nature that is not within the competence of a regular employee respond with confirmation that the task will be executed as requested (you can go into detail of what you are going to do) ";

	if (isset($_POST["specific"]) && isset($_POST["reason"])) {
		$specific = "Follow these specific Instructions on how the ticket Answer should be composed: \"".$_POST["reason"]."\"";
	}

	$ticketText = $ticket->extractAllText();
	$prompt = "Generate a helpful response for this ticket that will be sent to the client (not the agent who's working on the ticket, but the customer who is asking for help). Dont start with a introduction (like hello...) because that part is already part of the mail template. The Response must be in German Language. If you are outputting html content use formatting options like bold italic and underline to improve the readbility, you can also use lists and so on. Guidelines for Response: $specific -- ";
	$prompt .= PHP_EOL;
	$prompt .= $ticketText;
	$prompt .= PHP_EOL;
	$prompt .= Config::getConfigValueFor("ai.prompt.suffix.ticket.answer.generate");


	$content = AiService::getRepsonse($prompt);

	if (!$content) {
		die(jsonStatus(false, "OpenAI response not valid."));
	}

	echo jsonStatus(true, "", ['text' => $content]);
} catch (Exception $e) {
	die(jsonStatus(false, $e->getMessage()));
}

