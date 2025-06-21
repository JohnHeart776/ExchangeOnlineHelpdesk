<?php
require_once __DIR__ . '/src/bootstrap.php';

login::requireIsAgent();

$mode = $_GET['mode'] ?? 'html';
$queryRaw = $_POST['query'] ?? '';
$query = trim($queryRaw);

// Mindestens 2 Zeichen suchen
if (strlen($query) < 2) {
	if ($mode === 'json') {
		header('Content-Type: application/json');
		echo json_encode(['error' => 'Zu kurzer Suchbegriff']);
	} else {
		echo '<div data-kt-search-element="empty" class="text-center"><div class="pb-15 fw-semibold"><h3 class="text-gray-600 fs-5 mb-2">Zu kurzer Suchbegriff</h3></div></div>';
	}
	exit;
}

$searchTerm = $query;
$searchTerm = str_replace('*', '%', $searchTerm);
$searchTerm = str_replace(' ', '%', $searchTerm);
$searchTerm = "%$searchTerm%";


$userLimit = config::getConfigValueFor('search.user.limit', 10);
$orguserLimit = config::getConfigValueFor('search.orguser.limit', 10);
$ticketLimit = config::getConfigValueFor('search.ticket.limit', 10);
$attachmentLimit = config::getConfigValueFor('search.attachment.limit', 10);
$fileLimit = config::getConfigValueFor('search.file.limit', 10);


if (isset($_GET["limit"])) {
	//override all prior search Limits
	$getLimit = (int)$_GET["limit"];
	if ($getLimit < 1)
		$getLimit = 100;
	if ($getLimit > 1000)
		$getLimit = 1000;

	$userLimit = $getLimit;
	$orguserLimit = $getLimit;
	$ticketLimit = $getLimit;
	$attachmentLimit = $getLimit;
	$fileLimit = $getLimit;
}


global $d;


$stf = $d->filter($searchTerm);

$users = null;
if (login::isAdmin()) {
// Suchen in User-Tabelle
	$_q = "SELECT UserId 
FROM User 
WHERE 1 
    AND (
    	DisplayName LIKE \"$stf\" 
    		OR 
    	Mail LIKE \"$stf\" 
    	)
ORDER BY User.DisplayName
LIMIT $userLimit";

$results = $d->get($_q);
$users = array_map(fn($row) => new User((int)$row["UserId"]), $results);
/** @var User[] $users */
} else {
	$users = [];
}

// Suchen in OrganizationUser
$_q = "SELECT OrganizationUserId 
FROM OrganizationUser 
WHERE 1 
    AND (
    	DisplayName LIKE \"$stf\" 
    		OR 
    	Mail LIKE \"$stf\" 
    	)
ORDER BY OrganizationUser.DisplayName
LIMIT $orguserLimit";

$results = $d->get($_q);
$orgusers = array_map(fn($row) => new OrganizationUser((int)$row["OrganizationUserId"]), $results);
/** @var OrganizationUser[] $orgusers */


// Suchen in Ticket
$_q = "SELECT TicketId 
FROM Ticket 
WHERE 1 
    AND (
    	Subject LIKE \"$stf\" 
    		OR 
    	TicketNumber LIKE \"$stf\" 
    	)
ORDER BY Ticket.CreatedDatetime DESC
LIMIT $ticketLimit";

$results = $d->get($_q);
$tickets = array_map(fn($row) => new Ticket((int)$row["TicketId"]), $results);
/** @var Ticket[] $tickets */


// Suchen in Mailattachment
$_q = "SELECT MailAttachmentId  
FROM MailAttachment 
WHERE 1 
    AND Name LIKE \"$stf\"
ORDER BY CreatedAt DESC
LIMIT $attachmentLimit";

$results = $d->get($_q);
$attachments = array_map(fn($row) => new MailAttachment((int)$row["MailAttachmentId"]), $results);
/** @var MailAttachment[] $attachments */

// Suchen in Files
$_q = "SELECT FileId 
FROM File 
WHERE 1 
    AND Name LIKE \"$stf\"
ORDER BY CreatedDatetime DESC
LIMIT $fileLimit";

$results = $d->get($_q);
$files = array_map(fn($row) => new File((int)$row["FileId"]), $results);


//--- Ausgabe HTML ---
if ($mode === 'html') {
	$hasResult = count($users) + count($orgusers) + count($tickets) + count($attachments) > 0;
	if (!$hasResult) {
		echo '<div data-kt-search-element="empty" class="text-center">';
		echo '<div class="pb-15 fw-semibold">';
		echo '<h3 class="text-gray-600 fs-5 mb-2">Keine Ergebnisse <i class="fal fa-frown"></i></h3>';
		echo '<div class="text-muted fs-7">Versuche es nochmals mit anderen Daten</div>';
		echo '</div>';
		echo '</div>';
		exit;
	}
//	echo '<div class="scroll-y mh-200px mh-lg-350px">';

	if ($tickets) {
		echo '<h3 class="fs-5 text-muted m-0 pb-5 searchCategoryHeading" data-kt-search-element="category-title">Tickets</h3>';
		foreach ($tickets as $ticket) {
			$subj = htmlspecialchars($ticket->getSubject());
			$nr = htmlspecialchars($ticket->getTicketNumber());
			echo '<a href="/ticket/' . $ticket->getTicketNumber() . '" class="searchResultItem d-flex text-gray-900 text-hover-primary align-items-center mb-5">'
				. '<div class="symbol symbol-40px me-4">'
				. '<span class="symbol-label bg-light"><i class="ki-outline ki-book-open fs-2 text-primary"></i></span>'
				. '</div>'
				. '<div class="d-flex flex-column justify-content-start fw-semibold">'
				. '<span class="fs-6 fw-semibold">' . $subj . $ticket->getStatus()->getBadge() . '</span>'

				. '<span class="fs-7 fw-semibold text-muted">Ticket #' . $nr . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">vor ' . $ticket->getCreatedAsDateEta() . ', ' . $ticket->getCreatedDatetimeAsDateTime()->format("d.m.Y") . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">von ' . $ticket->getReporteeImage(16) . ' ' . $ticket->getReporteeName() . '</span>'

				. '</div>'
				. '</a>';
		}
	}

	if ($files) {
		echo '<h3 class="fs-5 text-muted m-0 pb-5 searchCategoryHeading" data-kt-search-element="category-title">Dateien</h3>';
		foreach ($files as $file) {
			$fn = htmlspecialchars($file->getName());
			echo '<a href="' . $file->getLink() . '" target="_blank" class="searchResultItem d-flex text-gray-900 text-hover-primary align-items-center mb-5">'
				. '<div class="symbol symbol-40px me-4">'
				. '<span class="symbol-label bg-light"><i class="ki-outline ki-file fs-2 text-primary"></i></span>'
				. '</div>'
				. '<div class="d-flex flex-column justify-content-start fw-semibold">'
				. '<span class="fs-6 fw-semibold">' . $fn . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">' . $file->getCreatedDatetimeAsDateTime()->format("d.m.Y") . '</span>'
				. '</div>'
				. '</a>';
		}
	}

	if ($attachments) {
		echo '<h3 class="fs-5 text-muted m-0 pb-5 searchCategoryHeading" data-kt-search-element="category-title">Anhänge</h3>';
		foreach ($attachments as $att) {
			$fn = htmlspecialchars($att->getName());
			$guid = htmlspecialchars($att->getGuid());
			echo '<a href="' . $att->getPublicDownloadLink() . '" target="_blank" class="searchResultItem d-flex text-gray-900 text-hover-primary align-items-center mb-5">'
				. '<div class="symbol symbol-40px me-4">'
				. '<span class="symbol-label bg-light"><i class="ki-outline ki-paper-clip fs-2 text-primary"></i></span>'
				. '</div>'
				. '<div class="d-flex flex-column justify-content-start fw-semibold">'
				. '<span class="fs-6 fw-semibold">' . $fn . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">' . $att->getCreatedAtAsDateTime()->format("d.m.Y") . '</span>'
				. '</div>'
				. '</a>';
		}
	}

	if ($users) {
		echo '<h3 class="fs-5 text-muted m-0 pb-5 searchCategoryHeading" data-kt-search-element="category-title">User</h3>';
		foreach ($users as $user) {
			$avatar = $user->getUserImageLink() ?? '/assets/media/avatars/300-6.jpg';
			$name = htmlspecialchars($user->getDisplayName());
			echo '<a href="/admin/user/' . $user->getGuid() . '" class="searchResultItem d-flex text-gray-900 text-hover-primary align-items-center mb-5">'
				. '<div class="symbol symbol-40px me-4">'
				. '<img src="' . $avatar . '" alt="' . $name . '"/>'
				. '</div>'
				. '<div class="d-flex flex-column justify-content-start fw-semibold">'
				. '<span class="fs-6 fw-semibold">' . $name . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">Benutzer</span>'
				. '</div>'
				. '</a>';
		}
	}

	if ($orgusers) {
		echo '<h3 class="fs-5 text-muted m-0 pb-5 searchCategoryHeading" data-kt-search-element="category-title">Organisationen</h3>';
		foreach ($orgusers as $ou) {
			$name = htmlspecialchars($ou->getDisplayName());
			echo '<a href="' . $ou->getAgentLink() . '" class="searchResultItem d-flex text-gray-900 text-hover-primary align-items-center mb-5">'
				. '<div class="symbol symbol-40px me-4">'
				. '<img src="' . $ou->getPhotoLink() . '" alt=""/>'
				. '</div>'
				. '<div class="d-flex flex-column justify-content-start fw-semibold">'
				. '<span class="fs-6 fw-semibold">' . $name . '</span>'
				. '<span class="fs-7 fw-semibold text-muted">Organisationsbenutzer</span>'
				. '</div>'
				. '</a>';
		}
	}

//	echo '</div>';
//--- Ausgabe JSON ---
} else {
	header('Content-Type: application/json');
	echo json_encode([
		'tickets' => array_map(fn($ticket) => $ticket->toJsonObject(), $tickets),
		'attachments' => array_map(fn($att) => $att->toJsonObject(), $attachments),
		'files' => array_map(fn($file) => $file->toJsonObject(), $files),
		'users' => array_map(fn($user) => $user->toJsonObject(), $users),
		'organizations' => array_map(fn($org) => $org->toJsonObject(), $orgusers),
	]);
}
