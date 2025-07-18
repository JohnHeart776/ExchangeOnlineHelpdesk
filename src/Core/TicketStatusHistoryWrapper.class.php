<?php

/**
 * Wrapper class for managing ticket status history and calculating time differences
 * between status changes.
 */
class TicketStatusHistoryWrapper
{
    private int $ticketId;
    private array $statusHistory = [];
    private bool $loaded = false;

    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
    }

    /**
     * Load all status changes for this ticket from the database
     */
    private function loadStatusHistory(): void
    {
        if ($this->loaded) {
            return;
        }

        global $d;
        $_q = "SELECT `TicketStatusId`, `Guid`, `CreatedDatetime`, `TicketId`, `OldStatusId`, `OldStatusIdIsFinal`, `NewStatusId`, `NewStatusIdIsFinal`, `UserId`, `Comment` 
               FROM `TicketStatus` 
               WHERE `TicketId` = " . $d->filter($this->ticketId) . " 
               ORDER BY `CreatedDatetime` ASC";
        
        $results = $d->get($_q);
        
        $this->statusHistory = [];
        if (!empty($results)) {
            foreach ($results as $row) {
                $ticketStatus = new TicketStatus(0);
                $ticketStatus->TicketStatusId = (int)$row['TicketStatusId'];
                $ticketStatus->Guid = $row['Guid'];
                $ticketStatus->CreatedDatetime = $row['CreatedDatetime'];
                $ticketStatus->TicketId = (int)$row['TicketId'];
                $ticketStatus->OldStatusId = (int)$row['OldStatusId'];
                $ticketStatus->OldStatusIdIsFinal = (int)$row['OldStatusIdIsFinal'];
                $ticketStatus->NewStatusId = (int)$row['NewStatusId'];
                $ticketStatus->NewStatusIdIsFinal = (int)$row['NewStatusIdIsFinal'];
                $ticketStatus->UserId = (int)$row['UserId'];
                $ticketStatus->Comment = $row['Comment'];
                
                $this->statusHistory[] = $ticketStatus;
            }
        }
        
        $this->loaded = true;
    }

    /**
     * Get all status changes for this ticket
     * @return TicketStatus[]
     */
    public function getStatusHistory(): array
    {
        $this->loadStatusHistory();
        return $this->statusHistory;
    }

    /**
     * Get the most recent status change
     * @return TicketStatus|null
     */
    public function getLatestStatusChange(): ?TicketStatus
    {
        $this->loadStatusHistory();
        return empty($this->statusHistory) ? null : end($this->statusHistory);
    }

    /**
     * Get the first status change (ticket creation)
     * @return TicketStatus|null
     */
    public function getFirstStatusChange(): ?TicketStatus
    {
        $this->loadStatusHistory();
        return empty($this->statusHistory) ? null : reset($this->statusHistory);
    }

    /**
     * Get status change by index (0-based)
     * @param int $index
     * @return TicketStatus|null
     */
    public function getStatusChangeByIndex(int $index): ?TicketStatus
    {
        $this->loadStatusHistory();
        return $this->statusHistory[$index] ?? null;
    }

    /**
     * Get the number of status changes
     * @return int
     */
    public function getStatusChangeCount(): int
    {
        $this->loadStatusHistory();
        return count($this->statusHistory);
    }

    /**
     * Calculate time difference between two status changes
     * @param int $fromIndex Index of the first status change
     * @param int $toIndex Index of the second status change
     * @return DateInterval|null Time difference or null if invalid indices
     */
    public function getTimeBetweenStatusChanges(int $fromIndex, int $toIndex): ?DateInterval
    {
        $this->loadStatusHistory();
        
        $fromStatus = $this->getStatusChangeByIndex($fromIndex);
        $toStatus = $this->getStatusChangeByIndex($toIndex);
        
        if (!$fromStatus || !$toStatus) {
            return null;
        }
        
        $fromDateTime = $fromStatus->getCreatedDatetimeAsDateTime();
        $toDateTime = $toStatus->getCreatedDatetimeAsDateTime();
        
        return $fromDateTime->diff($toDateTime);
    }

    /**
     * Calculate time difference between the first and last status changes
     * @return DateInterval|null Total time span or null if insufficient data
     */
    public function getTotalTimeSpan(): ?DateInterval
    {
        $this->loadStatusHistory();
        
        if (count($this->statusHistory) < 2) {
            return null;
        }
        
        return $this->getTimeBetweenStatusChanges(0, count($this->statusHistory) - 1);
    }

    /**
     * Calculate time spent in a specific status
     * @param int $statusId The status ID to calculate time for
     * @return DateInterval|null Time spent in status or null if not found
     */
    public function getTimeInStatus(int $statusId): ?DateInterval
    {
        $this->loadStatusHistory();
        
        $totalTime = new DateInterval('PT0S'); // Start with 0 seconds
        $statusStartTime = null;
        
        foreach ($this->statusHistory as $statusChange) {
            // If we're entering the target status
            if ($statusChange->getNewStatusIdAsInt() === $statusId) {
                $statusStartTime = $statusChange->getCreatedDatetimeAsDateTime();
            }
            // If we're leaving the target status and we have a start time
            elseif ($statusStartTime && $statusChange->getOldStatusIdAsInt() === $statusId) {
                $endTime = $statusChange->getCreatedDatetimeAsDateTime();
                $timeInStatus = $statusStartTime->diff($endTime);
                
                // Add this time period to the total
                $totalSeconds = $totalTime->s + $totalTime->i * 60 + $totalTime->h * 3600 + $totalTime->days * 86400;
                $periodSeconds = $timeInStatus->s + $timeInStatus->i * 60 + $timeInStatus->h * 3600 + $timeInStatus->days * 86400;
                
                $totalTime = DateInterval::createFromDateString($totalSeconds + $periodSeconds . ' seconds');
                $statusStartTime = null;
            }
        }
        
        // If we're still in the status, calculate time until now
        if ($statusStartTime) {
            $now = new DateTime();
            $timeInStatus = $statusStartTime->diff($now);
            
            $totalSeconds = $totalTime->s + $totalTime->i * 60 + $totalTime->h * 3600 + $totalTime->days * 86400;
            $periodSeconds = $timeInStatus->s + $timeInStatus->i * 60 + $timeInStatus->h * 3600 + $timeInStatus->days * 86400;
            
            $totalTime = DateInterval::createFromDateString($totalSeconds + $periodSeconds . ' seconds');
        }
        
        return $totalTime;
    }

    /**
     * Get status changes filtered by user
     * @param int $userId
     * @return TicketStatus[]
     */
    public function getStatusChangesByUser(int $userId): array
    {
        $this->loadStatusHistory();
        
        return array_filter($this->statusHistory, function($statusChange) use ($userId) {
            return $statusChange->getUserIdAsInt() === $userId;
        });
    }

    /**
     * Get status changes within a date range
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return TicketStatus[]
     */
    public function getStatusChangesInDateRange(DateTime $startDate, DateTime $endDate): array
    {
        $this->loadStatusHistory();
        
        return array_filter($this->statusHistory, function($statusChange) use ($startDate, $endDate) {
            $changeDate = $statusChange->getCreatedDatetimeAsDateTime();
            return $changeDate >= $startDate && $changeDate <= $endDate;
        });
    }

    /**
     * Check if ticket has ever been in a specific status
     * @param int $statusId
     * @return bool
     */
    public function hasBeenInStatus(int $statusId): bool
    {
        $this->loadStatusHistory();
        
        foreach ($this->statusHistory as $statusChange) {
            if ($statusChange->getNewStatusIdAsInt() === $statusId) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the current status from the history
     * @return int|null Current status ID or null if no history
     */
    public function getCurrentStatusId(): ?int
    {
        $latestChange = $this->getLatestStatusChange();
        return $latestChange ? $latestChange->getNewStatusIdAsInt() : null;
    }

    /**
     * Format time interval as human-readable string
     * @param DateInterval $interval
     * @return string
     */
    public static function formatTimeInterval(DateInterval $interval): string
    {
        $parts = [];
        
        if ($interval->days > 0) {
            $parts[] = $interval->days . ' day' . ($interval->days > 1 ? 's' : '');
        }
        if ($interval->h > 0) {
            $parts[] = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
        }
        if ($interval->i > 0) {
            $parts[] = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        }
        if ($interval->s > 0 || empty($parts)) {
            $parts[] = $interval->s . ' second' . ($interval->s > 1 ? 's' : '');
        }
        
        return implode(', ', $parts);
    }
}