{{-- Left Sidebar Start --}}
<div class="startbar d-print-none">
    <div class="brand">
        <a href="{{ route('dashboard') }}" class="logo">
            <span><img src="{{ asset('assets/images/logo-sm.png') }}" alt="logo-small" class="logo-sm"></span>
            <span>
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>

    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label mt-2"><span>CRM Menu</span></li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="iconoir-report-columns menu-icon"></i><span>Dashboard</span>
                        </a>
                    </li>

                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calls.*') ? 'active' : '' }}" href="{{ route('calls.index') }}">
                            <i class="iconoir-phone menu-icon"></i><span>Calls</span>
                        </a>
                    </li> --}}

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}" href="{{ route('whatsapp.index') }}">
                            <i class="iconoir-phone menu-icon"></i><span>Call Logs</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}" href="#leadsMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('leads.*') ? 'true' : 'false' }}">
                            <i class="iconoir-user menu-icon"></i><span>Leads</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('leads.*') ? 'show' : '' }}" id="leadsMenu">
                            <ul class="nav flex-column ms-4">
                                <li><a class="nav-link py-2 {{ request()->routeIs('leads.create') ? 'active' : '' }}" href="{{ route('leads.create') }}"><i class="iconoir-plus-circle me-1"></i>Add Lead</a></li>
                                <li><a class="nav-link py-2 {{ request()->routeIs('leads.index') ? 'active' : '' }}" href="{{ route('leads.index') }}"><i class="iconoir-list me-1"></i>All Leads</a></li>
                                <li>
    <a class="nav-link py-2 {{ request()->routeIs('leads.closed') ? 'active' : '' }}"
       href="{{ route('leads.closed') }}">
        <i class="iconoir-lock me-1"></i>Closed Leads
    </a>
</li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('followups.*') ? 'active' : '' }}" href="{{ route('followups.index') }}">
                            <i class="iconoir-calendar menu-icon"></i><span>Follow-ups</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#reportsMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                            <i class="iconoir-stats-report menu-icon"></i><span>Reports</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reportsMenu">
                            <ul class="nav flex-column ms-4">
                                <li><a class="nav-link py-2" href="{{ route('reports.lead') }}">Lead Report</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.admission') }}">Admission Report</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.user-wise-admission') }}">User Wise Admission</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.source-wise') }}">Source Wise</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.followup') }}">Follow-up Report</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.pending-followup') }}">Pending Follow-up</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.course-wise') }}">Service Wise</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.standard-wise') }}">Level Wise</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.lost-lead') }}">Lost Lead</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.conversion') }}">Conversion</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.counsellor-performance') }}">Counsellor Performance</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.fee-quotation') }}">Fee Quotation</a></li>
                                <li><a class="nav-link py-2" href="{{ route('reports.re-engagement') }}">Re-engagement</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="#settingsMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}">
                            <i class="iconoir-settings menu-icon"></i><span>Settings</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}" id="settingsMenu">
                            <ul class="nav flex-column ms-4">
                                <li>
    <a class="nav-link py-2" href="{{ route('settings.users.index') }}">
        <i class="iconoir-user me-1"></i>User List
    </a>
</li>
                                <li><a class="nav-link py-2" href="{{ route('settings.standards.index') }}"><i class="iconoir-book me-1"></i>Add Level</a></li>
                                <li><a class="nav-link py-2" href="{{ route('settings.followup-types.index') }}"><i class="iconoir-calendar-plus me-1"></i>Add Follow-up Type</a></li>
                                <li><a class="nav-link py-2" href="{{ route('settings.courses.index') }}"><i class="iconoir-learning me-1"></i>Add Services</a></li>
                                <li><a class="nav-link py-2" href="{{ route('settings.lead-statuses.index') }}"><i class="iconoir-check-circle me-1"></i>Add Lead Status</a></li>
                                <li><a class="nav-link py-2" href="{{ route('settings.lead-sources.index') }}"><i class="iconoir-share-android me-1"></i>Add Lead Source</a></li>
                                <li><a class="nav-link py-2" href="{{ route('settings.lead-priorities.index') }}"><i class="iconoir-star me-1"></i>Add Lead Priority</a></li>
                                <li>
    <a class="nav-link py-2 {{ request()->routeIs('settings.closed-statuses.*') ? 'active' : '' }}"
       href="{{ route('settings.closed-statuses.index') }}">
        <i class="iconoir-check-circle me-1"></i>Closed Status
    </a>
</li>
                                <li>
    <a class="nav-link py-2" href="{{ route('settings.whatsapp-templates.index') }}">
         <i class="iconoir-chat-bubble me-1"></i>
        WhatsApp Template
    </a>
</li>

{{--<li>
    <a class="nav-link py-2" href="{{ route('settings.app-settings.index') }}">
        <i class="iconoir-settings me-1"></i>
        App Settings
    </a>
</li>--}}
                            </ul>
                        </div>
                    </li>
                </ul>

                <div class="update-msg text-center">
                    <div class="d-flex justify-content-center align-items-center thumb-lg update-icon-box rounded-circle mx-auto">
                        <img src="{{ asset('assets/images/extra/party.gif') }}" alt="" class="d-inline-block me-1" height="30">
                    </div>
                    <h5 class="mt-3">Tuition CRM</h5>
                    <p class="mb-3 text-muted">Calls, leads, follow-ups, admissions and reports.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="startbar-overlay d-print-none"></div>
{{-- Left Sidebar End --}}
