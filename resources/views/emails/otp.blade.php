<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign to {{ config('app.name') }}</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #000; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style=" padding: 30px; border-radius: 5px;">
        <h1 style="color: #000; margin-top: 0;">Sign to {{ config('app.name') }}</h1>

        <p>You requested to sign in to Looksharp. Your one-time code is:</p>

        <div
            style="background-color: #ffffff; border: 2px solid #000; border-radius: 5px; padding: 20px; text-align: center; margin: 20px 0;">
            <p style="font-size: 32px; font-weight: bold; color: #000; margin: 0; letter-spacing: 5px;">{{ $otp }}
            </p>
        </div>

        <p>This code will expire in {{ $expiryMinutes }} minutes.</p>

        <p style="color: #000; font-size: 14px;">If you didn't request this code, please ignore this email.</p>
    </div>

    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #000;">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>
