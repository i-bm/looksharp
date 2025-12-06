<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body class="email-body" style="color: #333;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        {!! $content !!}
    </div>
    <div class="email-footer" style="color: #666;">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>
</html>

