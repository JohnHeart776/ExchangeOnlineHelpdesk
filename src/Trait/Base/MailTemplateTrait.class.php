<?php

/**
 * Base trait for mail template functionality
 * Provides common mail template methods used across multiple traits
 */
trait MailTemplateTrait
{
    /**
     * Get the mail template start content from configuration
     * 
     * @return string
     */
    protected function getMailTemplateStart(): string
    {
        return Config::getConfigValueFor("mail.template.start") ?? '';
    }
    
    /**
     * Get the mail template end content from configuration
     * 
     * @return string
     */
    protected function getMailTemplateEnd(): string
    {
        return Config::getConfigValueFor("mail.template.end") ?? '';
    }
    
    /**
     * Replace placeholders in mail text using MailHelper
     * 
     * @param string $text The text containing placeholders
     * @param mixed $organizationUser Optional organization user for placeholder replacement
     * @param mixed $ticket Optional ticket for placeholder replacement
     * @return string
     */
    protected function replacePlaceholders(string $text, $organizationUser = null, $ticket = null): string
    {
        // Use provided entities or try to get them from current object
        if ($organizationUser === null && method_exists($this, 'getOrganizationUser')) {
            $organizationUser = $this->getOrganizationUser();
        }
        
        if ($ticket === null && method_exists($this, 'getTicket')) {
            $ticket = $this->getTicket();
        }
        
        return MailHelper::renderMailText(
            text: $text,
            organizationUser: $organizationUser,
            ticket: $ticket
        );
    }
    
    /**
     * Wrap mail content with template start and end
     * 
     * @param string $content The main content
     * @return string
     */
    protected function wrapWithMailTemplate(string $content): string
    {
        return $this->getMailTemplateStart() . $content . $this->getMailTemplateEnd();
    }
    
    /**
     * Send a styled mail message using MailHelper
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlContent HTML content
     * @param string|null $from Optional sender email
     * @return bool
     */
    protected function sendStyledMail(string $to, string $subject, string $htmlContent, ?string $from = null): bool
    {
        return MailHelper::sendStyledMailFromSystemAccount(
            $from,
            $to,
            $subject,
            $htmlContent
        );
    }
}