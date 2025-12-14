<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New employer company submitted</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">New employer company submitted</h1>

        <p>A company profile has been submitted for review:</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $company->legal_name }}</strong></p>
            <p style="margin: 6px 0 0 0; color: #666;">Company ID: {{ $company->id }}</p>
        </div>

        <p>Review it in the admin dashboard.</p>
    </div>

    <div class="email-footer">
        <p>{{ config('app.name') }}</p>
    </div>
</body>

</html>

