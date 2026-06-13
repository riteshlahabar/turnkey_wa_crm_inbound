@extends('layouts.app')

@section('title','Dashboard')

@section('content')
@include('crm._page_header', [
    'title' => 'Dashboard',
    'subtitle' => 'Overall CRM records and user-wise performance'
])

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm text-white overflow-hidden h-100"
             style="background: linear-gradient(135deg, #0d6efd, #4dabf7); border-radius: 18px;">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold">Total Leads</p>
                        <h2 class="mb-0 fw-bold">{{ $cards['total_leads'] ?? 0 }}</h2>
                        <small class="text-white-50">Active/current leads</small>
                    </div>

                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:58px;height:58px;background:rgba(255,255,255,0.22);">
                        <i class="iconoir-user-love fs-2"></i>
                    </div>
                </div>

                <div style="position:absolute;right:-25px;bottom:-25px;width:90px;height:90px;background:rgba(255,255,255,0.12);border-radius:50%;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm text-white overflow-hidden h-100"
             style="background: linear-gradient(135deg, #198754, #51cf66); border-radius: 18px;">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold">Total Follow-ups</p>
                        <h2 class="mb-0 fw-bold">{{ $cards['total_followups'] ?? 0 }}</h2>
                        <small class="text-white-50">All active follow-ups</small>
                    </div>

                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:58px;height:58px;background:rgba(255,255,255,0.22);">
                        <i class="iconoir-calendar fs-2"></i>
                    </div>
                </div>

                <div style="position:absolute;right:-25px;bottom:-25px;width:90px;height:90px;background:rgba(255,255,255,0.12);border-radius:50%;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm text-white overflow-hidden h-100"
             style="background: linear-gradient(135deg, #f59f00, #ffd43b); border-radius: 18px;">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold">Pending Follow-ups</p>
                        <h2 class="mb-0 fw-bold">{{ $cards['pending_followups'] ?? 0 }}</h2>
                        <small class="text-white-50">Need action</small>
                    </div>

                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:58px;height:58px;background:rgba(255,255,255,0.22);">
                        <i class="iconoir-clock fs-2"></i>
                    </div>
                </div>

                <div style="position:absolute;right:-25px;bottom:-25px;width:90px;height:90px;background:rgba(255,255,255,0.12);border-radius:50%;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm text-white overflow-hidden h-100"
             style="background: linear-gradient(135deg, #dc3545, #ff6b6b); border-radius: 18px;">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold">Closed Leads</p>
                        <h2 class="mb-0 fw-bold">{{ $cards['closed_leads'] ?? 0 }}</h2>
                        <small class="text-white-50">Completed records</small>
                    </div>

                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:58px;height:58px;background:rgba(255,255,255,0.22);">
                        <i class="iconoir-check-circle fs-2"></i>
                    </div>
                </div>

                <div style="position:absolute;right:-25px;bottom:-25px;width:90px;height:90px;background:rgba(255,255,255,0.12);border-radius:50%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header text-white border-0"
         style="background: linear-gradient(135deg, #143622, #0f766e);">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 text-white">User-wise Leads & Follow-ups</h5>
                <small class="text-white-50">Counsellor/user performance overview</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:42px;height:42px;background:rgba(255,255,255,0.18);">
                <i class="iconoir-stats-up-square fs-4"></i>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Total Leads</th>
                    <th>Today Leads</th>
                    <th>Total Follow-ups</th>
                    <th>Today Follow-ups</th>
                    <th>Pending Follow-ups</th>
                    <th>Overdue</th>
                    <th>Closed Leads</th>
                    <th>Next Follow-up</th>
                </tr>
            </thead>

            <tbody>
                @forelse($userWiseReports as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ ucfirst($user->role ?? '-') }}</small>
                        </td>

                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $user->total_leads_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-info-subtle text-info">
                                {{ $user->today_leads_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-success-subtle text-success">
                                {{ $user->total_followups_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $user->today_followups_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-warning-subtle text-warning">
                                {{ $user->pending_followups_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-danger-subtle text-danger">
                                {{ $user->overdue_followups_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-dark-subtle text-dark">
                                {{ $user->closed_leads_count ?? 0 }}
                            </span>
                        </td>

                        <td>
                            @if($user->next_followup_at)
                                {{ \Carbon\Carbon::parse($user->next_followup_at)->format('d M Y h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No user-wise records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection