@extends('layouts.dashboard.main')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 700px; margin: 0 auto; text-align: center;">
    <div style="background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Success Icon -->
        <div style="width: 80px; height: 80px; background: #4CAF50; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
            <span style="color: white; font-size: 40px;">✓</span>
        </div>

        <h1 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 32px; color: var(--title-color); margin-bottom: 20px;">
            Profile Complete!
        </h1>

        <p style="font-family: var(--font-suse); font-size: 18px; color: var(--text-color); line-height: 1.6; margin-bottom: 30px;">
            Congratulations! Your profile is {{ $progress['completeness_score'] }}% complete.
        </p>

        <!-- Completeness Score -->
        <div style="background: #f0f0f0; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 30px; max-width: 400px; margin-left: auto; margin-right: auto;">
            <div style="background: #4CAF50; height: 100%; width: {{ $progress['completeness_score'] }}%; transition: width 0.3s ease;"></div>
        </div>

        <!-- Next Steps -->
        <div style="background: #f9f9f9; padding: 20px; border-radius: 4px; margin-bottom: 30px; text-align: left;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">What's Next?</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">
                    <span style="color: #4CAF50; margin-right: 10px;">✓</span>
                    Browse available opportunities
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">
                    <span style="color: #4CAF50; margin-right: 10px;">✓</span>
                    Get matched with relevant internships
                </li>
                <li style="padding: 8px 0;">
                    <span style="color: #4CAF50; margin-right: 10px;">✓</span>
                    Start applying to positions
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('dashboard') }}" 
               class="primary-btn1 btn-hover" 
               style="text-decoration: none; padding: 12px 24px; display: inline-block;">
                Go to Dashboard
            </a>
            <a href="{{ route('profile.build') }}" 
               style="text-decoration: none; padding: 12px 24px; display: inline-block; border: 2px solid #2196F3; color: #2196F3; border-radius: 4px;">
                Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection

