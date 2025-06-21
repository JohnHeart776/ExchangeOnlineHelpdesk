<?php

class PromptHelper
{

	public static function generatePromptForTicketActionItems(Ticket $ticket): string
	{
		//this one only uses the first comment
		//		$ticketText = $ticket->getTicketComments()[0]->getText();

		$ticketText = $ticket->extractAllText();
		return "A new ticket has been received! " . PHP_EOL .
			"** Ticket Text Start **" . PHP_EOL .
			$ticketText . PHP_EOL .
			"** Ticket Text End **" . PHP_EOL .
			PHP_EOL .
			"Your task: Create a list of actions that you as a Service Agent should take to resolve the ticket. " .
			"Find concrete statements and problems in the ticket and incorporate them into the tasks." .
			"If specific systems are mentioned in the ticket, you can also name them in the tasks. " .
			"If you believe that information is missing which is not present in the ticket, add this at the beginning of the tasks." .
			"Output format: Json Array in format [ { title: \"...\", description: \" ...\" },{ title: \"...\", description: \" ...\" }, ... ] - Title should be rather short, Description a bit longer";
	}

	public static function cleanupPrompt(mixed $prompt): string
	{
		if (!is_string($prompt)) {
			return '';
		}

		$cleaned = trim($prompt);
		$cleaned = preg_replace('/\s+/', ' ', $cleaned);
		return str_replace(["\r\n", "\r", "\n"], ' ', $cleaned);
	}

	public static function generatePropmptForArticleGeneration(string $topic)
	{
		$prompt = Config::get("ai.prompt.article.generate") . $topic;
		return $prompt;
	}
}
