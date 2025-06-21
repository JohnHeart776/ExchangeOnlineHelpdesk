<?php

trait StatusTrait
{

	public function getBadge()
	{
		return '<span class="fw-medium badge badge-light-' . $this->getColor() . '"><i class="fad ' . $this->getIcon() . ' me-2"></i>' . $this->getPublicName() . '</span>';
	}

	public function isOpen(): bool
	{
		return $this->getIsOpenAsInt() > 0;
	}

	public function toJsonObject()
	{
		return [
			"guid" => $this->getGuid(),
			"name" => $this->getPublicName(),
			"color" => $this->getColor(),
			"icon" => $this->getIcon(),
		];
	}

	/**
	 * @return bool
	 */
	public function hasCustomerNotificationTemplate(): bool
	{
		return $this->getCustomerNotificationTemplateIdAsInt() > 0;
	}

	/**
	 * @return bool
	 */
	public function hasAgentNotificationTemplate(): bool
	{
		return $this->getAgentNotificationTemplateIdAsInt() > 0;
	}

	/**
	 * @return NotificationTemplate
	 */
	public function getCustomerNotificationTemplate(): NotificationTemplate
	{
		return new NotificationTemplate($this->getCustomerNotificationTemplateIdAsInt());
	}

	/**
	 * @return NotificationTemplate
	 */
	public function getAgentNotificationTemplate(): NotificationTemplate
	{
		return new NotificationTemplate($this->getAgentNotificationTemplateIdAsInt());
	}

	public function isFinal()
	{
		return $this->getIsFinalAsInt() > 0;
	}

	public function isClosed()
	{
		return $this->isFinal();
	}

	public function isDefaultAssignedStatus(): bool
	{
		return $this->equals(TicketStatusHelper::getDefaultAssignedStatus());
	}

	public function isDefaultCustomerReplyStatus(): bool
	{
		return $this->equals(TicketStatusHelper::getDefaultCustomerReplyStatus());
	}

	public function isDefaultClosedStatus(): bool
	{
		return $this->equals(TicketStatusHelper::getDefaultClosedStatus());
	}

	public function isDefaultResolvedStatus(): bool
	{
		return $this->equals(TicketStatusHelper::getDefaultResolvedStatus());
	}

	public function isDefaultWorkingStatus(): bool
	{
		return $this->equals(TicketStatusHelper::getDefaultWorkingStatus());
	}

	/**
	 * @param string $internalName
	 * @return Status
	 * @throws \Database\DatabaseQueryException
	 */
	public static function byInternalName(string $internalName): Status
	{
		return StatusController::searchOneBy("InternalName", $internalName);
	}


}