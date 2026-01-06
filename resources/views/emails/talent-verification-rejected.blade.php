<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Verification Rejected</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">Profile Verification Rejected</h1>

        <p>We're sorry, but your profile verification has been rejected.</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $profile->full_name }}</strong></p>
            <p style="margin: 6px 0 0 0; color: #666;">Verification Status: <span style="color: #ef4444; font-weight: bold;">Rejected</span></p>
        </div>

        @if($reason)
        <p style="margin-top: 16px;"><strong>Reason for rejection:</strong></p>
        <div style="background-color: #f8f9fa; border: 1px solid #eee; border-radius: 6px; padding: 12px;">
            {{ $reason }}
        </div>
        @endif

        <p style="margin-top: 16px;">What you can do:</p>
        <ul>
            <li>Review the reason for rejection above</li>
            <li>Submit a new verification document</li>
            <li>Ensure your document is clear and valid</li>
        </ul>

        <p style="margin-top: 16px;">Log in to submit a new verification document:</p>
        <p><a href="{{ route('talent.profile.verification.show') }}">{{ route('talent.profile.verification.show') }}</a></p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

