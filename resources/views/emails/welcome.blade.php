<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #000; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="padding: 30px; border-radius: 5px;">
        <h1 style="color: #000; margin-top: 0;">Welcome to {{ config('app.name') }}!</h1>

        <p>Hi{{ $user->first_name ? ' ' . $user->first_name : '' }},</p>

        <p>We're thrilled to have you join {{ config('app.name') }}! Your account has been successfully created.</p>

        @if($user->user_type === 'talent')
            <p>As a Talent member, you can now:</p>
            <ul style="color: #000;">
                <li>Build your professional profile and showcase your skills</li>
                <li>Browse and apply for internships, attachments, and graduate trainee positions</li>
                <li>Connect with top employers in Ghana</li>
                <li>Track your application status in real-time</li>
            </ul>
            <p>Get started by completing your profile to increase your chances of getting matched with the best opportunities!</p>
        @elseif($user->user_type === 'employer')
            <p>As an Employer, you can now:</p>
            <ul style="color: #000;">
                <li>Create your company profile and showcase your organization</li>
                <li>Post internship, attachment, and graduate trainee opportunities</li>
                <li>Connect with talented students and recent graduates</li>
                <li>Manage applications and schedule interviews</li>
            </ul>
            <p>Start by setting up your company profile and posting your first opportunity!</p>
        @else
            <p>You're all set! Log in to explore all the features {{ config('app.name') }} has to offer.</p>
        @endif

        <p>If you have any questions, feel free to reach out to our support team. We're here to help!</p>

        <p style="color: #000; font-size: 14px;">Happy exploring!</p>
    </div>

    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #000;">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>
