<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            if (! in_array(config('app.env'), ['local', 'testing'])) {
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

            } else {
                // Mail::raw($content, function ($message) use ($to, $subject) {
                //     $message->to($to)
                //         ->subject($subject)
                //         ->from($this->formatFromField());

                //     Log::info('Email sent successfully via Mail', [
                //         'to' => $to,
                //         'subject' => $subject,
                //         'mail_message' => $message,
                //     ]);
                // });

                Log::info('Email sent successfully via Mail', [
                    'to' => $to,
                    'subject' => $subject,
                    'mail_message' => $content,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'data' => $result ?? null,
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
            if (! in_array(config('app.env'), ['local', 'testing'])) {
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
            } else {

                // Mail::raw($textContent, function ($message) use ($to) {
                //     $message->to($to)
                //         ->subject('Sign to Looksharp')
                //         ->from($this->formatFromField());
                // });

                Log::info('OTP email sent successfully via Mail', [
                    'to' => $to,
                    'user_type' => $userType,
                    'mail_message' => $textContent,
                ]);
            }

            return [
                'success' => true,
                'message' => 'OTP email sent successfully',
                'data' => $result ?? null,
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
     * Send welcome email to newly registered user.
     *
     * @param  \App\Models\User  $user  The user to send welcome email to
     * @return array Response array with 'success' and 'message' keys
     *
     * @throws \Exception
     */
    public function sendWelcomeEmail(\App\Models\User $user): array
    {
        try {
            // Render the welcome email content from the Blade template
            $htmlContent = View::make('emails.welcome', [
                'user' => $user,
            ])->render();

            // Create a plain text version for email clients that don't support HTML
            $userName = $user->first_name ? $user->first_name : 'there';
            $textContent = 'Welcome to '.config('app.name', 'Looksharp')."!\n\n"
                ."Hi {$userName},\n\n"
                ."We're thrilled to have you join ".config('app.name', 'Looksharp')."! Your account has been successfully created.\n\n"
                .'Log in to explore all the features '.config('app.name', 'Looksharp')." has to offer.\n\n"
                ."If you have any questions, feel free to reach out to our support team. We're here to help!\n\n"
                ."Happy exploring!\n\n"
                ."Thanks,\n"
                .config('app.name', 'Looksharp').' Team';

            if (! in_array(config('app.env'), ['local', 'testing'])) {
                $result = Resend::emails()->send([
                    'from' => $this->formatFromField(),
                    'to' => [$user->email],
                    'subject' => 'Welcome to '.config('app.name', 'Looksharp'),
                    'html' => $htmlContent,
                    'text' => $textContent,
                ]);

                Log::info('Welcome email sent successfully via Resend', [
                    'to' => $user->email,
                    'user_type' => $user->user_type,
                    'resend_id' => $result->id ?? null,
                ]);
            } else {
                Log::info('Welcome email sent successfully via Mail', [
                    'to' => $user->email,
                    'user_type' => $user->user_type,
                    'mail_message' => $textContent,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Welcome email sent successfully',
                'data' => $result ?? null,
            ];

        } catch (ErrorException $e) {
            Log::error('Resend API error sending welcome email', [
                'to' => $user->email,
                'from' => $this->formatFromField(),
                'from_address' => $this->fromAddress,
                'from_name' => $this->fromName,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send welcome email via Resend: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error('Welcome email sending failed', [
                'to' => $user->email,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send welcome email: {$e->getMessage()}");
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
