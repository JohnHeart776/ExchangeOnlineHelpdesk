<?php
require_once __DIR__ . '/../../src/bootstrap.php';


Login::requireIsAgent();

$ticket = new Ticket($_POST["ticket"]);
if (!$ticket->isValid())
	die(jsonStatus(false, "Ticket not found."));

if (!isset($_POST['target']))
	die(jsonStatus(false, "Target parameter is required."));

$dueDate = new DateTime();
$applySla = true;
switch ($_POST['target']) {
	case "24h":
		$dueDate = new DateTime("+24 hours");
		break;
	case 'tomorrow':
		$dueDate->modify('+1 day');
		break;
	case 'endweek':
		$dueDate->modify('next friday');
		break;
	case 'nextweek':
		$dueDate->modify('next monday');
		break;
	case 'nextmonth':
		$dueDate->modify('first day of next month');
		break;
	case 'nextyear':
		$dueDate->modify('first day of next year');
		break;
	case 'ai':
		$dueDate = $ticket->determineDueDateWithAi();
		$applySla = false;
		break;
	case 'custom':
		if (!isset($_POST['date']))
			die(jsonStatus(false, "Custom date parameter is required for custom target."));
		$s = $_POST['date'] . ":00";
		$s = str_replace("T", " ", $s);
		$customDate = DateTime::createFromFormat("Y-m-d H:i:s", $s, new DateTimeZone('UTC'));
		if (!$customDate)
			die(jsonStatus(false, "Invalid custom date format. Expected format: YYYY-MM-DDThh:mm"));
		if ($customDate->format('Y-m-d\TH:i') !== $_POST['date'])
			die(jsonStatus(false, "Invalid date values provided."));
		$dueDate = $customDate;
		$applySla = false;
		break;
	default:
		die(jsonStatus(false, "Invalid target parameter."));
}
if ($applySla)
	$dueDate = SlaHelper::getSlaDueDate($dueDate);

$post_custom = $_POST['target'] === 'custom';
$ticket->setDueDate($dueDate);
$ticket->spawn();

die(jsonStatus(true, "Due date updated successfully.", [
	"ticket" => $ticket->toJsonObject(),
	"post_custom" => $post_custom,
]));

