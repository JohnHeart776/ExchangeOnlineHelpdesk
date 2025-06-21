<?php

class TextHelper
{

	public static function wrapPlainTextInHtml(?string $text): string
	{
		if ($text === null) {
			return '';
		}

		$text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);

		// Convert URLs to clickable links
		$text = preg_replace('/(https?:\/\/[^\s<]+)/', '<a href="$1" target="_blank">$1</a>', $text);

		// Convert email addresses to mailto links 
		$text = preg_replace('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', '<a href="mailto:$1">$1</a>', $text);

		// Format lists
		$text = preg_replace('/^[\-\*]\s+(.+?)$/m', '<li>$1</li>', $text);
		$text = preg_replace('/((?:<li>.+?<\/li>\n?)+)/', '<ul>$1</ul>', $text);

		// Format numbered lists
		$text = preg_replace('/^\d+\.\s+(.+?)$/m', '<li>$1</li>', $text);
		$text = preg_replace('/((?:<li>.+?<\/li>\n?)+)/', '<ol>$1</ol>', $text);

		// Add paragraphs for text blocks
		$text = '<p>' . preg_replace('/\n\n+/', '</p><p>', $text) . '</p>';

		// Convert remaining line breaks
		return nl2br($text);
	}

}
