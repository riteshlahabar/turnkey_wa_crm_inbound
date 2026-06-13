<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">
<head>
    <meta charset="utf-8" />

    @php
        $appName = "Sane's Academy CRM";
        $loginLogo = asset('assets/images/logo-sm.png');
        $favicon = asset('assets/images/favicon.ico');

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('crm_app_settings')) {
                $appSetting = \Illuminate\Support\Facades\DB::table('crm_app_settings')->first();

                if ($appSetting) {
                    $appName = $appSetting->app_name ?: $appName;

                    if (!empty($appSetting->login_logo)) {
                        $loginLogo = asset('storage/' . $appSetting->login_logo);
                    } elseif (!empty($appSetting->app_logo)) {
                        $loginLogo = asset('storage/' . $appSetting->app_logo);
                    }

                    if (!empty($appSetting->favicon)) {
                        $favicon = asset('storage/' . $appSetting->favicon);
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback static logo
        }
    @endphp

    <title>Login | {{ $appName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="{{ $appName }}" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ $favicon }}">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.18), transparent 28%),
                #f4f7fb;
        }

        .auth-card {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .auth-header-box {
            background: linear-gradient(135deg, #13294b 0%, #0f766e 55%, #16a34a 100%) !important;
        }

        .auth-logo-wrap {
            height: 86px;
            width: 86px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.14);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.22);
        }

        .auth-logo {
            max-height: 62px;
            max-width: 70px;
            object-fit: contain;
        }

        .login-title {
            color: #ffffff;
            letter-spacing: 0.2px;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.78) !important;
        }

        .form-control {
            min-height: 44px;
            border-radius: 12px;
        }

        .btn-primary {
            min-height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f766e, #16a34a);
            border: 0;
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #115e59, #15803d);
        }

        .contact-admin-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 12px;
        }
    </style>
</head>

<body>
<div class="container-xxl">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mx-auto">
                        <div class="card auth-card">
                            <div class="card-body p-0 auth-header-box rounded-top">
                                <div class="text-center p-4">
                                    <a href="{{ route('login') }}" class="logo logo-admin text-decoration-none">
                                        <span class="auth-logo-wrap">
                                            <img src="{{ $loginLogo }}" alt="logo" class="auth-logo">
                                        </span>
                                    </a>

                                    <h4 class="mt-3 mb-1 fw-semibold fs-18 login-title">
                                        Let's Get Started
                                    </h4>

                                    <p class="fw-medium mb-0 login-subtitle">
                                        Sign in to continue {{ $appName }}.
                                    </p>
                                </div>
                            </div>

                            <div class="card-body pt-0 px-4 pb-4">

                                @if ($errors->any())
                                    <div class="alert alert-danger mt-3">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success mt-3">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger mt-3">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form class="my-4" method="POST" action="{{ route('login.submit') }}">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="email">Email</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="Enter email"
                                            required
                                            autofocus
                                        >
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label" for="password">Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="password"
                                            name="password"
                                            placeholder="Enter password"
                                            required
                                        >
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-sm-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="remember"
                                                    name="remember"
                                                    value="1"
                                                >
                                                <label class="form-check-label" for="remember">
                                                    Remember me
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 text-end">
                                            {{-- Forgot password disabled --}}
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="submit">
                                                    Log In <i class="fas fa-sign-in-alt ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center mb-1 contact-admin-box">
                                    <p class="text-muted mb-0">
                                        Don't have an account?
                                        <span class="text-primary ms-1 fw-semibold">Contact Admin</span>
                                    </p>
                                </div>

                            </div>
                        </div>

                        <p class="text-center text-muted mt-3 mb-0">
                            © {{ date('Y') }} {{ $appName }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>