@extends('layouts.auth.main')

@section('content')
<div class="container" style="padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <h1
            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 32px; color: var(--title-color); margin-bottom: 20px;">
            Welcome to Looksharp!
        </h1>
        <p
            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.6; margin-bottom: 30px;">
            Your account has been created successfully. The profile building page will be available soon.
        </p>
        <a href="{{ route('home') }}" class="primary-btn1 btn-hover"
            style="text-decoration: none; display: inline-block; padding: 12px 24px;">
            Go to Dashboard
        </a>
    </div>
</div>
@endsection
