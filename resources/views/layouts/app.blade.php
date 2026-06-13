<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | InBound</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="InBound Call Management System" name="description" />
    <meta name="author" content="Turnkey Infotech" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dark / Light mode saved theme apply before CSS load --}}
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('crm-theme-mode');
                var theme = savedTheme === 'dark' ? 'dark' : 'light';

                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-startbar', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.setAttribute('data-startbar', 'light');
            }
        })();
    </script>

    {{-- App favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    {{-- App CSS --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
</head>
<body>

    {{-- Include Header --}}
    @include('layouts.header')

    {{-- Include Sidebar --}}
    @include('layouts.sidebar')

    {{-- Page Wrapper --}}
    <div class="page-wrapper">
        {{-- Page Content --}}
        <div class="page-content">
            <div class="container-fluid">
                {{-- Display Success/Error Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="iconoir-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="iconoir-warning-circle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="iconoir-warning-circle me-1"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Main Content Area --}}
                @yield('content')
            </div>
            {{-- end container-fluid --}}
        </div>
        {{-- end page-content --}}

        {{-- Include Footer --}}
        @include('layouts.footer')
    </div>
    {{-- end page-wrapper --}}

    {{-- JavaScript --}}
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    {{-- Save Dark / Light mode after toggle click --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleButton = document.getElementById('light-dark-mode');

            if (toggleButton) {
                toggleButton.addEventListener('click', function () {
                    setTimeout(function () {
                        var currentTheme = document.documentElement.getAttribute('data-bs-theme');

                        if (currentTheme === 'dark') {
                            localStorage.setItem('crm-theme-mode', 'dark');
                            document.documentElement.setAttribute('data-startbar', 'dark');
                        } else {
                            localStorage.setItem('crm-theme-mode', 'light');
                            document.documentElement.setAttribute('data-startbar', 'light');
                        }
                    }, 50);
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>