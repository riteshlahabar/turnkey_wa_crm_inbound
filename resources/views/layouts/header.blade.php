{{-- Top Bar Start --}}
<div class="topbar d-print-none">
    <div class="container-fluid">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">
            {{-- Left Side: Menu Toggle + Welcome Text --}}
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu"></i>
                    </button>
                </li>
                <li class="mx-2 welcome-text">
                    <h5 class="mb-0 fw-semibold text-truncate">
                        @php
                            $hour = now('Asia/Kolkata')->hour;
                            $greeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : ($hour < 21 ? 'Evening' : 'Night'));
                        @endphp
                        Good {{ $greeting }}, Turnkey Infotech
                    </h5>
                    
                </li>
            </ul>

            {{-- Right Side: Notifications, User Menu --}}
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                {{-- Light/Dark Mode Toggle --}}
                <li class="topbar-item">
                    <a class="nav-link nav-icon" href="javascript:void(0)" id="light-dark-mode">
                        <i class="iconoir-half-moon dark-mode"></i>
                        <i class="iconoir-sun-light light-mode"></i>
                    </a>
                </li>

                {{-- Notifications Dropdown --}}
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" 
                       data-bs-toggle="dropdown" 
                       href="#" 
                       role="button" 
                       aria-haspopup="false" 
                       aria-expanded="false" 
                       data-bs-offset="0,19">
                        <i class="iconoir-bell"></i>
                        <span class="alert-badge"></span>
                    </a>
                    <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">
                        <h5 class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center">
                            Notifications
                            <a href="#" class="badge text-body-tertiary badge-pill">
                                <i class="iconoir-plus-circle fs-4"></i>
                            </a>
                        </h5>
                        <div class="ms-0" style="max-height: 230px" data-simplebar>
                            {{-- Notification Item --}}
                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">2 min ago</small>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                        <i class="iconoir-phone fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark fs-13">New Incoming Call</h6>
                                        <small class="text-muted mb-0">Phone: +91-9876543210</small>
                                    </div>
                                </div>
                            </a>

                            <a href="#" class="dropdown-item py-3">
                                <small class="float-end text-muted ps-2">10 min ago</small>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-success-subtle text-success thumb-md rounded-circle">
                                        <i class="iconoir-send-mail fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2 text-truncate">
                                        <h6 class="my-0 fw-normal text-dark fs-13">WhatsApp Sent</h6>
                                        <small class="text-muted mb-0">German Course message sent</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <a href="{{ route('calls.index') }}" class="dropdown-item text-center text-dark fs-13 py-2">
                            View All <i class="fi-arrow-right"></i>
                        </a>
                    </div>
                </li>

                {{-- User Profile Dropdown --}}
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" 
                       data-bs-toggle="dropdown" 
                       href="#" 
                       role="button" 
                       aria-haspopup="false" 
                       aria-expanded="false" 
                       data-bs-offset="0,19">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" 
     alt="Logo" 
     class="thumb-md rounded-circle"
     style="object-fit:contain; padding:4px; background:#fff;">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/logo-sm.png') }}" 
     alt="Logo" 
     class="thumb-md rounded-circle"
     style="object-fit:contain; padding:4px; background:#fff;">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">{{ Auth::user()->name ?? 'Admin User' }}</h6>
                                <small class="text-muted mb-0">{{ Auth::user()->email ?? 'admin@inbound.com' }}</small>
                            </div>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Account</small>
                        <a class="dropdown-item" href="{{ route('settings.users.index') }}">
                            <i class="las la-user fs-18 me-1 align-text-bottom"></i> Profile
                        </a>
                        <small class="text-muted px-2 py-1 d-block">Settings</small>
                        <a class="dropdown-item" href="{{ route('settings.standards.index') }}">
                            <i class="las la-cog fs-18 me-1 align-text-bottom"></i> Account Settings
                        </a>
                        <div class="dropdown-divider mb-0"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>
{{-- Top Bar End --}}
