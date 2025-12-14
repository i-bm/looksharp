@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">System Settings</h1>
            <p class="dashboard-page-subtitle">Manage system configuration and preferences</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">General Settings</h2>
                <p class="dashboard-card-subtitle">Basic system configuration</p>
            </div>
        </div>
        <div class="dashboard-card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="system_name" class="form-label">System Name</label>
                    <input type="text" class="form-control" id="system_name" name="system_name"
                        value="{{ $settings['system_name'] ?? config('app.name') }}" readonly>
                    <small class="form-text text-muted">System name is configured in application config files.</small>
                </div>

                <div class="mb-3">
                    <label for="system_email" class="form-label">System Email</label>
                    <input type="email" class="form-control" id="system_email" name="system_email"
                        value="{{ $settings['system_email'] ?? config('mail.from.address') }}" readonly>
                    <small class="form-text text-muted">System email is configured in mail configuration files.</small>
                </div>

                <div class="dashboard-empty-state"
                    style="text-align: center; padding: 2rem; border-top: 1px solid #eee; margin-top: 2rem;">
                    <i class="bi bi-gear" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <p style="color: #666; margin: 0;">Settings management features are coming soon. This section will
                        allow you to configure system-wide settings and preferences.</p>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" disabled>Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection