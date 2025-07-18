<?php

class TicketTextCleaner
{
	public static function clean(string $html): string
	{
		$enableInscriptis = Config::getConfigValueFor("inscriptis.enable");
		if ($enableInscriptis) {
			$client = new InscriptisClient(Config::getConfigValueFor("inscriptis.server"));
			$text = $client->convertHtmlToText($html);
			$text = self::applyDatabaseReplacements($text);
		} else {
			$text = self::cleanOld($html);
		}
		return $text;
	}

	/**
	 * Converts HTML to clean plain text and replaces known text patterns.
	 */
	public static function cleanOld(string $html): string
	{
		// Create DOM document and load HTML (with error suppression for malformed HTML)
		$dom = new DOMDocument('1.0', 'UTF-8');
		libxml_use_internal_errors(true);
		// Set UTF-8 header to ensure correct display of special characters and umlauts.
		$htmlContent = $html;
		if (stripos($html, '<html') === false) {
			// If no complete HTML document exists, add basic structure
			$htmlContent = "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"></head><body>{$html}</body></html>";
		}
		$dom->loadHTML($htmlContent);
		libxml_clear_errors();

		// XPath für DOM erstellen
		$xpath = new DOMXPath($dom);

		// Remove all invisible/irrelevant nodes:
		// Script, style, head area and comment nodes are deleted from the DOM.
		foreach ($xpath->query('//script|//style|//noscript|//template|//head|//title|//meta|//base|//comment()') as $node) {
			$node->parentNode?->removeChild($node);
		}
		// Remove elements that are invisible via CSS (display:none or visibility:hidden)
		foreach ($xpath->query('//*[@style]') as $node) {
			$style = $node->getAttribute('style');
			if (stripos($style, 'display:none') !== false || stripos($style, 'visibility:hidden') !== false) {
				$node->parentNode?->removeChild($node);
			}
		}
		// Remove elements with HTML5 hidden attribute or aria-hidden="true"
		foreach ($xpath->query('//*[@hidden] | //*[@aria-hidden="true"]') as $node) {
			$node->parentNode?->removeChild($node);
		}
		// Remove form elements that don't make sense in plain text
		foreach ($xpath->query('//input|//button|//select|//textarea') as $node) {
			$node->parentNode?->removeChild($node);
		}

		// Recursive helper function that converts a DOMNode to plain text.
		$nodeToText = function (\DOMNode $node, int $listLevel = 0) use (&$nodeToText): string {
			$text = '';
			if ($node instanceof \DOMText) {
				// Text node: Get content
				$content = $node->wholeText;
				if (!preg_match('/^\s+$/', $content)) {
					$content = preg_replace('/[ \t\r\n]+/', ' ', $content) ?? $content;
				}
				return $content;
			}
			if ($node instanceof \DOMElement) {
				$tag = strtolower($node->tagName);
				switch ($tag) {
					case 'br':
						// Line break tag
					return "\n";
					case 'p':
					case 'div':
					case 'section':
					case 'article':
					case 'header':
					case 'footer':
					case 'aside':
					case 'nav':
					case 'address':
					case 'blockquote':
						$blockText = '';
						foreach ($node->childNodes as $child) {
							$blockText .= $nodeToText($child, $listLevel);
						}
						$blockText = trim($blockText);
						if ($blockText !== '') {
							$text .= $blockText;
						}
						$text .= "\n";
						return $text;
					case 'ul':
					case 'ol':
						$isOrdered = ($tag === 'ol');
						$index = 1;
						foreach ($node->childNodes as $child) {
							if ($child instanceof \DOMElement && strtolower($child->tagName) === 'li') {
								// Determine prefix based on list type and indentation
								$prefix = str_repeat('    ', $listLevel)  // 4 spaces per list nesting level
									. ($isOrdered ? ($index . '. ') : '- ');
								$itemText = '';
								foreach ($child->childNodes as $grandChild) {
									$itemText .= $nodeToText($grandChild, $listLevel + 1);
								}
								$itemText = trim($itemText);
								$text .= $prefix . $itemText . "\n";
								$index++;
							} else {
								$text .= $nodeToText($child, $listLevel);
							}
						}
						return $text;
					case 'li':
						foreach ($node->childNodes as $child) {
							$text .= $nodeToText($child, $listLevel);
						}
						return trim($text) . "\n";
					case 'table':
						foreach ($node->childNodes as $child) {
							$tagName = ($child instanceof \DOMElement) ? strtolower($child->tagName) : '';
							if ($tagName === 'tbody' || $tagName === 'thead' || $tagName === 'tfoot') {
								foreach ($child->childNodes as $tr) {
									if ($tr instanceof \DOMElement && strtolower($tr->tagName) === 'tr') {
										$text .= $nodeToText($tr, $listLevel);
									}
								}
							} else if ($tagName === 'tr') {
								$text .= $nodeToText($child, $listLevel);
							} else {
								$text .= $nodeToText($child, $listLevel);
							}
						}
						return $text;
					case 'tr':
						$rowTextParts = [];
						foreach ($node->childNodes as $child) {
							$tagName = ($child instanceof \DOMElement) ? strtolower($child->tagName) : '';
							if ($tagName === 'td' || $tagName === 'th') {
								$cellText = '';
								foreach ($child->childNodes as $grandChild) {
									$cellText .= $nodeToText($grandChild, $listLevel);
								}
								$cellText = trim($cellText);
								$rowTextParts[] = $cellText;
							}
						}
						if (!empty($rowTextParts)) {
							$text .= implode("\t", $rowTextParts);
						}
						$text .= "\n";
						return $text;
					case 'td':
					case 'th':
						foreach ($node->childNodes as $child) {
							$text .= $nodeToText($child, $listLevel);
						}
						return $text;
					case 'a':
						$linkText = '';
						foreach ($node->childNodes as $child) {
							$linkText .= $nodeToText($child, $listLevel);
						}
						$linkText = trim($linkText);
						$href = $node->getAttribute('href');
						if ($href) {
							$hrefTrim = trim($href);
							if ($linkText === '' || strcasecmp($linkText, $hrefTrim) === 0) {
								$text .= $hrefTrim;
							} else {
								$text .= $linkText . " ({$hrefTrim})";
							}
						} else {
							$text .= $linkText;
						}
						return $text;
					case 'img':
						$alt = $node->getAttribute('alt');
						if ($alt) {
							$text .= $alt;
						}
						return $text;
					case 'pre':
					case 'code':
						$preText = $node->textContent ?? '';
						return $preText;
					default:
						foreach ($node->childNodes as $child) {
							$text .= $nodeToText($child, $listLevel);
						}
						return $text;
				}
			}
			if ($node->hasChildNodes()) {
				foreach ($node->childNodes as $child) {
					$text .= $nodeToText($child, $listLevel);
				}
			}
			return $text;
		};

		$bodyNode = $xpath->query('//body')->item(0);
		$rootNode = $bodyNode ?? $dom;
		$plainText = $nodeToText($rootNode);

		$plainText = str_replace("\u{00A0}", ' ', $plainText);
		$plainText = str_replace(
			["\u{200B}", "\u{200C}", "\u{200D}", "\u{200E}", "\u{200F}", "\u{FEFF}", "\u{00AD}"],
			'',
			$plainText
		);
		$plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		$plainText = str_replace("\r\n", "\n", $plainText);

		$plainText = self::applyDatabaseReplacements($plainText);

		$plainText = trim($plainText, "\r\n");

		while (preg_match('/\n{3,}/', $plainText)) {
			$plainText = preg_replace('/\n{3,}/', "\n\n", $plainText);
		}
		$plainText = explode("\n", $plainText);
		$plainText = array_map('trim', $plainText);
		$plainText = implode("\n", $plainText);

		$plainText = preg_replace('/\A(\s*\n)+/u', '', $plainText); // Empty lines at start
		$plainText = preg_replace('/(\s*\n)+\z/u', '', $plainText); // Empty lines at end

		$plainText = self::replaceSignatureBlocks($plainText);
		return $plainText;
	}

	public static function replaceSignatureBlocks(string $text): string
	{
		// Signatures
		$pattern = '/
		^([^\n]+)\n                # Name
		([^\n]+)\n                 # Position
		.*?                        # chars inbetween
		Tel\.\s*[^\d]*([\d\/\- ]*(\d{4,5})).*?  # Phone
		E-?mail:\s*([^\s<]+@[^>\s]+)            # Mail
	/imsx';

		$text = preg_replace_callback($pattern, function ($m) {
			$name = trim($m[1]);
			$position = trim($m[2]);
			$ext = trim($m[4]);
			$email = trim($m[5]);

			return "{$name} - {$position}\n{$ext} - {$email}";
		}, $text);

		return $text;
	}


	private static function extractSignatureInfo(string $text): ?string
	{
		preg_match('/^([^\n]+)\n([^\n]+)\n.*?Tel\.\s*[^\d]*([\d\/\- ]*(\d{3,5}))\b.*?E-?mail:\s*([^\s<]+@[^>\s]+)/is', $text, $matches);

		if (!$matches || count($matches) < 6) {
			return null;
		}

		$name = trim($matches[1]);
		$position = trim($matches[2]);
		$extension = trim($matches[4]); // Die letzte Nummerngruppe
		$email = trim($matches[5]);

		return "{$name} - {$position}\n{$extension} - {$email}";
	}


	/**
	 * Converts an HTML table to plain text.
	 */
	private static function renderTableText(\DOMNode $table): string
	{
		$rows = [];

		foreach ($table->getElementsByTagName('tr') as $tr) {
			$cells = [];

			foreach ($tr->getElementsByTagName('th') as $th) {
				$cells[] = trim($th->textContent);
			}
			foreach ($tr->getElementsByTagName('td') as $td) {
				$cells[] = trim($td->textContent);
			}

			$rows[] = $cells;
		}

		// Calculate column widths
		$colWidths = [];
		foreach ($rows as $row) {
			foreach ($row as $i => $cell) {
				$len = mb_strlen($cell);
				if (!isset($colWidths[$i]) || $len > $colWidths[$i]) {
					$colWidths[$i] = $len;
				}
			}
		}

		// Ausgabe zusammenbauen
		$lines = [];
		foreach ($rows as $row) {
			$line = '';
			foreach ($row as $i => $cell) {
				$width = $colWidths[$i] ?? 10;
				$line .= str_pad($cell, $width + 2); // +2 for padding
			}
			$lines[] = rtrim($line);
		}

		return implode("\n", $lines);
	}

	/**
	 * Applies defined text replacements from the database.
	 */
	private static function applyDatabaseReplacements(string $text): string
	{
		// Harmonize line breaks in target text
		$text = str_replace(["\r\n", "\r"], "\n", $text);

		foreach (TextReplaceController::getAll() as $textReplace) {
			$text = $textReplace->replaceIn($text);
		}

		return $text;
	}

}
