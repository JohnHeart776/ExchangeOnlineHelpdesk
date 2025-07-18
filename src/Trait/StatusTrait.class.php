<?php

trait StatusTrait
{
	use JsonSerializableTrait;
	use EntityRelationshipTrait;
	use BooleanCheckTrait;

	public function getBadge()
	{
		return '<span class="fw-medium badge badge-light-' . $this->getColor() . '"><i class="fad ' . $this->getIcon() . ' me-2"></i>' . $this->getPublicName() . '</span>';
	}

	public function isOpen(): bool
	{
		return $this->isBooleanFieldTrue('getIsOpenAsInt');
	}

	public function toJsonObject(): array
	{
		return array_merge($this->getBaseJsonFields(), [
			"name" => $this->getPublicName(),
			"color" => $this->getColor(),
			"icon" => $this->getIcon(),
		]);
	}

	/**
	 * @return bool
	 */
	public function hasCustomerNotificationTemplate(): bool
	{
		return $this->hasEntityById('getCustomerNotificationTemplateId');
	}

	/**
	 * @return bool
	 */
	public function hasAgentNotificationTemplate(): bool
	{
		return $this->hasEntityById('getAgentNotificationTemplateId');
	}

	/**
	 * @return NotificationTemplate
	 */
	public function getCustomerNotificationTemplate(): NotificationTemplate
	{
		return $this->getEntityById('NotificationTemplate', 'getCustomerNotificationTemplateId');
	}

	/**
	 * @return NotificationTemplate
	 */
	public function getAgentNotificationTemplate(): NotificationTemplate
	{
		return $this->getEntityById('NotificationTemplate', 'getAgentNotificationTemplateId');
	}

	public function isFinal()
	{
		return $this->isBooleanFieldTrue('getIsFinalAsInt');
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
