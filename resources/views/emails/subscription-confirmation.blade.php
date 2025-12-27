<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Confirmation</title>
</head>

<body class="email-body">
    <div class="email-container">
        <h1 class="email-title">Subscription Confirmation</h1>

        <p>Thank you for subscribing to {{ config('app.name') }}!</p>

        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 6px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0;"><strong>Subscription Details:</strong></p>
            <p style="margin: 6px 0 0 0;">
                <strong>Plan:</strong> {{ ucfirst($subscription->tier) }}
                @if($subscription->billing_cycle)
                ({{ ucfirst($subscription->billing_cycle) }})
                @endif
            </p>
            @if($subscription->amount > 0)
            <p style="margin: 6px 0 0 0;">
                <strong>Amount:</strong> {{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}
            </p>
            @endif
            @if($subscription->ends_at)
            <p style="margin: 6px 0 0 0;">
                <strong>Expires:</strong> {{ $subscription->ends_at->format('F d, Y') }}
            </p>
            @endif
            <p style="margin: 6px 0 0 0;">
                <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
            </p>
        </div>

        <p style="margin-top: 16px;">You can now access all features included in your subscription plan.</p>

        <p style="margin-top: 16px;">Log in to manage your subscription:</p>
        <p><a href="{{ route('employer.company.show') }}">View Company Profile</a></p>
    </div>

    <div class="email-footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>

