<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign to {{ config('app.name') }}</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">Sign to {{ config('app.name') }}</h1>

        <p>You requested to sign in to Looksharp. Your one-time code is:</p>

        <div style="background-color: #ffffff; border: 2px solid #000; border-radius: 5px; padding: 20px; text-align: center; margin: 20px 0;">
            <p style="font-size: 32px; font-weight: bold; color: #000; margin: 0; letter-spacing: 5px;">{{ $otp }}
            </p>
        </div>

        <p>This code will expire in {{ $expiryMinutes }} minutes.</p>

        <p class="email-footer-small">If you didn't request this code, please ignore this email.</p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>
