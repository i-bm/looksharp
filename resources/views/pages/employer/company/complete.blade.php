@extends('layouts.auth.main')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 900px; margin: 0 auto;">

    <!-- Header with Logo and Logout -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
        <a href="{{ route('dashboard') }}" style="text-decoration: none;">
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo" style="max-height: 40px;">
        </a>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit"
                style="background: #f0f0f0; border: 1px solid #ddd; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; color: #333; transition: background 0.2s;">
                Logout
            </button>
        </form>
    </div>

    <!-- Success Message -->
    <div
        style="background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 15px;">✓</div>
        <h2
            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 28px; color: #155724; margin-bottom: 10px;">
            Company Profile Complete!
        </h2>
        <p style="font-size: 16px; color: #155724; margin: 0;">
            You've successfully completed all steps of your company profile.
        </p>
    </div>

    <!-- Profile Summary -->
    <div
        style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3
            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 20px; color: var(--title-color); margin-bottom: 20px;">
            Profile Summary
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <strong style="color: #666; font-size: 14px;">Company Name:</strong>
                <p style="margin: 5px 0 0 0; font-size: 16px;">{{ $company->legal_name }}</p>
            </div>
            @if($company->trading_name)
            <div>
                <strong style="color: #666; font-size: 14px;">Trading Name:</strong>
                <p style="margin: 5px 0 0 0; font-size: 16px;">{{ $company->trading_name }}</p>
            </div>
            @endif
            @if($company->industry)
            <div>
                <strong style="color: #666; font-size: 14px;">Industry:</strong>
                <p style="margin: 5px 0 0 0; font-size: 16px;">{{ $company->industry }}</p>
            </div>
            @endif
            @if($company->city)
            <div>
                <strong style="color: #666; font-size: 14px;">Location:</strong>
                <p style="margin: 5px 0 0 0; font-size: 16px;">{{ $company->city }}, {{ $company->country ?? 'Ghana' }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Next Steps -->
    <div
        style="background: #f0f7ff; padding: 20px; border-radius: 8px; border-left: 4px solid #0066cc; margin-bottom: 30px;">
        <h3
            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 18px; color: var(--title-color); margin-bottom: 15px;">
            What's Next?
        </h3>
        <ul style="margin: 0; padding-left: 20px; color: #333;">
            <li style="margin-bottom: 10px;">Review your company profile information</li>
            <li style="margin-bottom: 10px;">Submit your profile for admin approval</li>
            <li style="margin-bottom: 10px;">Once approved, you can start posting opportunities</li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 15px;">
        <a href="{{ route('employer.company.show') }}" class="primary-btn1 btn-hover"
            style="flex: 1; padding: 12px 24px; font-size: 16px; text-align: center; text-decoration: none; display: inline-block;">
            View Company Profile
        </a>
        <a href="{{ route('employer.company.edit') }}" class="primary-btn2 btn-hover"
            style="flex: 1; padding: 12px 24px; font-size: 16px; text-align: center; text-decoration: none; display: inline-block;">
            Edit Profile
        </a>
    </div>
</div>
@endsection