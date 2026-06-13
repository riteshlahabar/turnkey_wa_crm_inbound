@extends('layouts.app')

@section('title', 'App Settings')

@section('content')
@include('crm._page_header', [
    'title' => 'App Settings',
    'subtitle' => 'Manage mobile app logo, splash logo, login logo and default profile image.'
])

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('settings.app-settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Mobile App Branding</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Institute / App Name <span class="text-danger">*</span></label>
                        <input type="text" name="app_name" value="{{ old('app_name', $setting->app_name) }}" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primary Color</label>
                            <input type="text" name="primary_color" value="{{ old('primary_color', $setting->primary_color) }}" class="form-control" placeholder="#0F5917">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="text" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color) }}" class="form-control" placeholder="#14802C">
                        </div>
                    </div>

                    <div class="row">
                        @foreach([
                            'app_logo' => 'App Logo',
                            'login_logo' => 'Login Logo',
                            'splash_logo' => 'Splash Logo',
                            'default_profile_image' => 'Default Profile Image',
                        ] as $field => $label)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="file" name="{{ $field }}" class="form-control" accept="image/*">
                                @if($setting->{$field})
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset($setting->{$field}) }}" alt="{{ $label }}" style="height:55px;width:55px;object-fit:contain;border-radius:12px;border:1px solid #e5e7eb;padding:4px;background:#fff;">
                                        <small class="text-muted">Current {{ strtolower($label) }}</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary">
                        <i class="iconoir-save-action-floppy me-1"></i>
                        Save App Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Mobile API</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Flutter app will fetch logo dynamically from:</p>
                    <code>GET /api/v1/app/settings</code>
                    <hr>
                    <p class="mb-1"><strong>Used in:</strong></p>
                    <ul class="mb-0">
                        <li>Splash screen</li>
                        <li>Login screen</li>
                        <li>Profile fallback image</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
