<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You have been invited</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">You’ve been invited</h1>

        <p>You’ve been invited to manage the company profile for:</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $company->legal_name }}</strong></p>
            @if($company->trading_name)
            <p style="margin: 6px 0 0 0; color: #666;">Trading as {{ $company->trading_name }}</p>
            @endif
        </div>

        <p>To get started, log in with this email address and request an OTP code:</p>

        <p><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>

        <p>After logging in, complete the company profile and submit it for admin approval.</p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

