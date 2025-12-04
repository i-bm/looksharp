<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Resend\Exceptions\ErrorException;
use Resend\Laravel\Facades\Resend;

class EmailService
{
    private string $fromAddress;

    private string $fromName;

    public function __construct()
    {
        $this->fromAddress = config('mail.from.address', 'no-reply@joinlooksharp.com');
        $this->fromName = config('mail.from.name', config('app.name', 'Looksharp'));

        // Validate email address format
        if (! filter_var($this->fromAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email address configured: {$this->fromAddress}. Please set a valid MAIL_FROM_ADDRESS in your .env file.");
        }
    }

    /**
     * Format the 'from' field for Resend API.
     * Returns either "email@example.com" or "Name <email@example.com>".
     *
     * @return string Properly formatted from field
     */
    private function formatFromField(): string
    {
        // Ensure email address is trimmed and valid
        $email = trim($this->fromAddress);

        // Validate email format one more time before sending
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::error('Invalid email address in formatFromField', [
                'email' => $email,
                'from_address' => $this->fromAddress,
            ]);
            throw new \Exception("Invalid email address format: {$email}");
        }

        // If name is empty or just whitespace, use only email
        $name = trim($this->fromName);
        if (empty($name)) {
            return $email;
        }

        // If name contains special characters that might break the format,
        // quote it properly
        if (preg_match('/[<>@,;:\[\]\\"]/', $name)) {
            // Quote the name and escape any quotes inside
            $quotedName = '"'.str_replace('"', '\\"', $name).'"';

            return "{$quotedName} <{$email}>";
        }

        // Standard format: Name <email@example.com>
        return "{$name} <{$email}>";
    }

    /**
     * Send email using Resend API directly.
     *
     * @param  string  $to  Recipient email address
     * @param  string  $subject  Email subject
     * @param  string  $content  Email content (HTML or plain text)
     * @param  string|null  $template  Optional template name (for future use)
     * @return array Response array with 'success' and 'message' keys
     *
     * @throws \Exception
     */
    public function send(string $to, string $subject, string $content, ?string $template = null): array
    {
        try {
            $result = Resend::emails()->send([
                'from' => $this->formatFromField(),
                'to' => [$to],
                'subject' => $subject,
                'html' => $content,
            ]);

            Log::info('Email sent successfully via Resend', [
                'to' => $to,
                'subject' => $subject,
                'resend_id' => $result->id ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'data' => $result,
            ];

        } catch (ErrorException $e) {
            Log::error('Resend API error', [
                'to' => $to,
                'subject' => $subject,
                'from' => $this->formatFromField(),
                'from_address' => $this->fromAddress,
                'from_name' => $this->fromName,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send email via Resend: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send email: {$e->getMessage()}");
        }
    }

    /**
     * Send OTP email using Resend API directly.
     *
     * @param  string  $to  Recipient email address
     * @param  string  $otp  OTP code
     * @param  string|null  $userType  Optional user type
     * @param  int  $expiryMinutes  OTP expiry time in minutes
     * @return array Response array with 'success' and 'message' keys
     *
     * @throws \Exception
     */
    public function sendOtp(string $to, string $otp, ?string $userType = null, int $expiryMinutes = 10): array
    {
        try {
            // Render the OTP email content from the Blade template
            $htmlContent = View::make('emails.otp', [
                'otp' => $otp,
                'userType' => $userType,
                'expiryMinutes' => $expiryMinutes,
            ])->render();

            // Create a plain text version for email clients that don't support HTML
            $textContent = 'Sign to '.config('app.name')."\n\n"
                ."You requested to sign in to Looksharp. Your one-time code is: {$otp}\n\n"
                ."This code will expire in {$expiryMinutes} minutes.\n\n"
                ."If you didn't request this code, please ignore this email.\n\n"
                ."Thanks,\n"
                .config('app.name').' Team';

            $result = Resend::emails()->send([
                'from' => $this->formatFromField(),
                'to' => [$to],
                'subject' => 'Sign to Looksharp',
                'html' => $htmlContent,
                'text' => $textContent,
            ]);

            Log::info('OTP email sent successfully via Resend', [
                'to' => $to,
                'user_type' => $userType,
                'resend_id' => $result->id ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'OTP email sent successfully',
                'data' => $result,
            ];

        } catch (ErrorException $e) {
            Log::error('Resend API error sending OTP', [
                'to' => $to,
                'from' => $this->formatFromField(),
                'from_address' => $this->fromAddress,
                'from_name' => $this->fromName,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send OTP email via Resend: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error('OTP email sending failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send OTP email: {$e->getMessage()}");
        }
    }

    /**
     * Convert plain text email to HTML format.
     *
     * @param  string  $text  Plain text content
     * @return string HTML formatted content
     */
    private function convertTextToHtml(string $text): string
    {
        // Convert line breaks to <br> tags
        $html = nl2br(e($text));

        // Wrap in a simple HTML structure
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
</head>
<body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;\">
    <div style=\"background-color: #f8f9fa; padding: 20px; border-radius: 5px;\">
        {$html}
    </div>
</body>
</html>";
    }
}
