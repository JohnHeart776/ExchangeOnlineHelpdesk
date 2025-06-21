<?php

class ReportingTicketsByCategory
{


	public DateTime $weekEnd;

	public function __construct(
		public DateTime $weekStart,
	)
	{
		$this->weekStart->setTime(0, 0, 0);
		$this->weekStart->modify('monday this week');
		$this->weekEnd = (clone $this->weekStart)->modify('sunday this week')->setTime(23, 59, 59);
	}

	public function getCategoryStats(): array
	{
		$_q = "
	        SELECT 
	            c.CategoryId,
	            c.PublicName as categoryName,
	            COUNT(t.TicketId) as count
	        FROM Category c
	        LEFT JOIN Ticket t ON t.CategoryId = c.CategoryId 
	            AND t.CreatedDatetime BETWEEN :start AND :end
	        GROUP BY c.CategoryId, c.PublicName
	        ORDER BY c.PublicName ASC;
	    ";

		global $d;

		$t = $d->getPDO($_q, [
			':start' => $this->weekStart->format(DateTimeInterface::ATOM),
			':end' => $this->weekEnd->format(DateTimeInterface::ATOM),
		]);

		$stats = [];
		foreach ($t as $row) {
			$stats[] = new CategoryStatsElement(
				CategoryController::getById($row["CategoryId"]),
				(int)$row["count"]
			);
		}

		return $stats;
	}



}

class CategoryStatsElement
{
	public function __construct(
		private Category $category,
		private int      $count,
	)
	{
	}

	public function getCategory(): Category
	{
		return $this->category;
	}

	public function getCount(): int
	{
		return $this->count;
	}
}
