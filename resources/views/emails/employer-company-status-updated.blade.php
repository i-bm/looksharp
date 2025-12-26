<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company review update</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">Company review update</h1>

        <p>Your company profile status has been updated:</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $company->legal_name }}</strong></p>
            <p style="margin: 6px 0 0 0; color: #666;">New status: {{ $company->status }}</p>
        </div>

        @if($company->review_notes)
        <p><strong>Notes from admin:</strong></p>
        <div style="background-color: #f8f9fa; border: 1px solid #eee; border-radius: 6px; padding: 12px;">
            {{ $company->review_notes }}
        </div>
        @endif

        <p style="margin-top: 16px;">Log in to view details and take action:</p>
        <p><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

