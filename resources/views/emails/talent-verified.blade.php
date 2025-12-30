<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Verification Approved</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">Profile Verification Approved</h1>

        <p>Great news! Your profile verification has been approved.</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>{{ $profile->full_name }}</strong></p>
            <p style="margin: 6px 0 0 0; color: #666;">Verification Status: <span style="color: #10b981; font-weight: bold;">Verified</span></p>
            @if($profile->verification_type)
            <p style="margin: 6px 0 0 0; color: #666;">Document Type: {{ ucfirst(str_replace('_', ' ', $profile->verification_type)) }}</p>
            @endif
        </div>

        <p style="margin-top: 16px;">You can now:</p>
        <ul>
            <li>Apply for jobs on the platform</li>
            <li>Show your verified badge on your profile</li>
            <li>Build trust with employers</li>
        </ul>

        <p style="margin-top: 16px;">Log in to view your profile:</p>
        <p><a href="{{ route('talent.profile.show') }}">{{ route('talent.profile.show') }}</a></p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

