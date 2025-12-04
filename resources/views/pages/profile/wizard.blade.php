@extends('layouts.dashboard.main')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 900px; margin: 0 auto;">
    <!-- Progress Bar -->
    <div style="margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 28px; color: var(--title-color);">
                Build Your Profile
            </h1>
            <div style="font-size: 16px; color: var(--text-color);">
                <strong>{{ $progress['completeness_score'] }}%</strong> Complete
            </div>
        </div>
        
        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden;">
            <div style="background: #4CAF50; height: 100%; width: {{ $progress['completeness_score'] }}%; transition: width 0.3s ease;"></div>
        </div>

        <!-- Step Indicators -->
        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            @for($i = 1; $i <= 4; $i++)
                <div style="flex: 1; text-align: center;">
                    <a href="{{ route('profile.build.step', ['step' => $i]) }}" 
                       style="text-decoration: none; color: {{ $i <= $current_step ? '#4CAF50' : '#999' }}; 
                              display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; 
                                    background: {{ $i < $current_step ? '#4CAF50' : ($i == $current_step ? '#2196F3' : '#f0f0f0') }};
                                    color: {{ $i <= $current_step ? '#fff' : '#999' }};
                                    display: flex; align-items: center; justify-content: center; 
                                    font-weight: bold; margin-bottom: 8px;">
                            @if($i < $current_step)
                                ✓
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        <span style="font-size: 12px;">
                            @if($i == 1) Basic Info
                            @elseif($i == 2) Education
                            @elseif($i == 3) Skills
                            @else Verification
                            @endif
                        </span>
                    </a>
                </div>
            @endfor
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Step Content -->
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        @if($current_step == 1)
            @include('pages.profile.steps.basic-info')
        @elseif($current_step == 2)
            @include('pages.profile.steps.education')
        @elseif($current_step == 3)
            @include('pages.profile.steps.skills')
        @elseif($current_step == 4)
            @include('pages.profile.steps.verification')
        @endif
    </div>
</div>
@endsection

