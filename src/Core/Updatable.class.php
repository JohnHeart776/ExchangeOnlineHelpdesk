<?php

class Updatable
{

	public static function Ticket(): array
	{
		return [
			"StatusId",
			"CategoryId",
			"UpdatedDatetime",
			"AssigneeUserId",
			"DueDatetime",
			"Subject",
		];
	}

	public static function OrganizationUser(): array
	{
		return [
			"DisplayName",
			"UserPrincipalName",
			"Mail",
			"GivenName",
			"Surname",
			"JobTitle",
			"Department",
			"MobilePhone",
			"OfficeLocation",
			"CompanyName",
			"BusinessPhones",
			"AccountEnabled",
			"EmployeeId",
			"SamAccountName",
			"Photo",
		];
	}

	public static function User(): array
	{
		return [
			"Enabled",
			"Upn",
			"DisplayName",
			"Name",
			"Surname",
			"Title",
			"Mail",
			"Telephone",
			"OfficeLocation",
			"CompanyName",
			"MobilePhone",
			"BusinessPhones",
			"AccountEnabled",
			"UserRole",
			"LastLogin",
		];
	}

	public static function UserImage(): array
	{
		return [
			"Base64Image",
			"LastUpdated",
		];
	}

	public static function MailAttachment(): array
	{
		return [
			"Name",
			"ContentType",
			"MailId",
			"ContentType",
			"Size",
			"IsInline",
			"Content",
			"TextRepresentation",
		];
	}

	public static function TicketComment(): array
	{
		return [
			"MailId",
			"Text",
			"TextType",
			"AccessLevel",
		];
	}

	public static function Config(): array
	{
		return [
			"Value",
		];
	}

	public static function Mail(): array
	{
		return [
			"HasAttachments",
			"TicketId",
		];
	}

	public static function TicketActionItem(): array
	{
		return [
			"Completed",
			"CompletedAt",
			"CompletedByUserId",
			"Comment",
			"DueDatetime",
		];
	}

	public static function Category(): array
	{
		return [
			"InternalName",
			"PublicName",
			"Icon",
			"Color",
			"SortOrder",
		];
	}

	public static function ActionGroup(): array
	{
		return [
			"Name",
			"Description",
			"SortOrder",
		];
	}

	public static function ActionItem(): array
	{
		return [
			"Title",
			"Description",
			"IsOptional",
			"SortOrder",
		];
	}

	public static function CategorySuggestion(): array
	{
		return [
			"CategoryId",
			"Enabled",
			"Priority",
			"Filter",
			"AutoClose",
		];
	}

	public static function TextReplace(): array
	{
		return [
			"SearchFor",
			"ReplaceBy",
			"Enabled",
		];
	}

	public static function MenuItem(): array
	{
		return [
			"Title",
			"Enabled",
			"SortOrder",
			"Color",
			"Icon",
			"Link",
			"ImageFileId",
			"requireIsUser",
			"requireIsAgent",
			"requireIsAdmin",
		];
	}

	public static function TemplateText(): array
	{
		return [
			"Name",
			"Description",
			"Content",
		];
	}

	public static function Status()
	{
		return [
			"PublicName",
			"Color",
			"CustomerNotificationTemplateId",
			"AgentNotificationTemplateId",
			"Icon",
		];
	}

	public static function NotificationTemplate()
	{
		return [
			"Enabled",
			"MailSubject",
			"MailText",
			"Name",
		];
	}

	public static function TicketFile()
	{
		return [
			"AccessLevel",
		];
	}

	public static function Article()
	{
		return [
			"Title",
			"Content",
			"UpdatedAtDatetime",
		];
	}

	public static function AiCache()
	{
		return [];
	}
}