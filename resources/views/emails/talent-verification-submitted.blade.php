<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Talent Verification Submission</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">New Talent Verification Submission</h1>

        <p>A new talent verification document has been submitted and requires review.</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $profile->full_name }}</strong></p>
            <p style="margin: 6px 0 0 0; color: #666;">Email: {{ $user->email ?? 'N/A' }}</p>
            @if($profile->student_id)
            <p style="margin: 6px 0 0 0; color: #666;">Student ID: {{ $profile->student_id }}</p>
            @endif
            @if($profile->verification_type)
            <p style="margin: 6px 0 0 0; color: #666;">Document Type: {{ ucfirst(str_replace('_', ' ', $profile->verification_type)) }}</p>
            @endif
            <p style="margin: 6px 0 0 0; color: #666;">Status: <span style="color: #f59e0b; font-weight: bold;">Pending</span></p>
        </div>

        <p style="margin-top: 16px;">Review the verification:</p>
        <p><a href="{{ route('admin.talent-verifications.show', ['id' => $profile->id]) }}">{{ route('admin.talent-verifications.show', ['id' => $profile->id]) }}</a></p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

