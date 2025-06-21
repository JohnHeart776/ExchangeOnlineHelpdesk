<?php
require_once __DIR__ . '/../src/bootstrap.php';
global $d;

$agents = array_filter(UserController::getAll(), fn($user) => $user->isAgent());

$_q = "SELECT t.TicketId 
FROM Ticket t 
LEFT JOIN Status s ON s.StatusId = t.StatusId
WHERE s.IsOpen = 1 
  AND t.CreatedDatetime < DATE_SUB(NOW(), INTERVAL 3 DAY)
  AND (t.AssigneeUserId IS NULL OR t.AssigneeUserId = 0)
  ORDER BY t.CreatedDatetime DESC";

$t = $d->get($_q);
if (empty($t))
	die("No unprocessed tickets found");

$tickets = array_map(fn($u) => new Ticket((int)$u['TicketId']), $t);

$emailBody = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #f5f5f5; padding: 20px; margin-bottom: 20px; }
        .ticket { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .ticket-number { font-weight: bold; color: #444; }
        .ticket-subject { font-size: 1.1em; margin: 5px 0; }
        .ticket-date { color: #666; }
        .ticket-messenger { margin: 5px 0; }
        .ticket-link { color: #0066cc; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orphaned Tickets</h1>
        <p>Tickets that exist for 3 days without an assignee</p>
    </div>';

foreach ($tickets as $ticket) {
	$emailBody .= '
    <div class="ticket">
        <div class="ticket-number">' . $ticket->getTicketNumber() . '</div>
        <div class="ticket-subject">' . $ticket->getSubject() . '</div>
        <div class="ticket-date">Reported: ' . $ticket->getCreatedDatetimeAsDateTime()->format('d.m.Y') . ' by ' . $ticket->getMessengerName() . '</div>
        <a class="ticket-link" href="' . $ticket->getAbsoluteLink() . '">Open ticket</a>
    </div>';
}

$emailBody .= '
</body>
</html>';

foreach ($agents as $agent) {
	echo "Sending Mail to " . $agent->getDisplayName() . "\n";
	$agent->sendMailMessage(count($tickets) . " Tickets without assignee", $emailBody);
}


	


