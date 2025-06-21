<?php

class TicketStatusHelper
{
	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefault = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultAssignedStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefaultAssignedStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultWorkingStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefaultWorkingStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultClosedStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefaultClosedStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultStatus(): ?Status
	{
		return new Status(self::getDefaultStatusId());
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultAssignedStatus(): ?Status
	{
		return new Status(self::getDefaultAssignedStatusId());
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultWorkingStatus(): ?Status
	{
		return new Status(self::getDefaultWorkingStatusId());
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultClosedStatus(): ?Status
	{
		return new Status(self::getDefaultClosedStatusId());
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDuplicateStatus(): ?Status
	{
		$internalName = Config::getConfigValueFor("status.duplicate.internalName");
		global $d;
		$_q = "SELECT StatusId FROM Status WHERE InternalName = :name LIMIT 1";
		$t = $d->getPDO($_q, [":name" => $internalName], true);
		if ($t) {
			return new Status((int)$t["StatusId"]);
		}
		return null;

	}

	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultCustomerReplyStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefaultCustomerReplyStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultCustomerReplyStatus(): ?Status
	{
		return new Status(self::getDefaultCustomerReplyStatusId());
	}


	/**
	 * @return int|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultWaitingForCustomerStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDetaultWaitingForCustomerStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return Status|null
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultWaitingForCustomerStatus(): ?Status
	{
		return new Status(self::getDefaultWaitingForCustomerStatusId());
	}

	public static function getDefaultResolvedStatusId(): ?int
	{
		global $d;
		$sql = "SELECT StatusId FROM Status WHERE IsDefaultSolvedStatus = 1 LIMIT 1";
		$row = $d->getPDO($sql, [], true);
		return $row ? (int)$row['StatusId'] : null;
	}

	/**
	 * @return Status
	 * @throws \Database\DatabaseQueryException
	 */
	public static function getDefaultResolvedStatus(): Status
	{
		return new Status(self::getDefaultResolvedStatusId());
	}


}
