@extends('layouts.app')

@section('title','Closed Leads')

@section('content')
@include('crm._page_header', [
    'title' => 'Closed Leads',
    'subtitle' => 'All completed/closed leads'
])

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('leads.closed') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Lead no/name/phone">
            </div>

            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date"
                       name="from_date"
                       class="form-control"
                       value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date"
                       name="to_date"
                       class="form-control"
                       value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">User</label>
                <select name="assigned_user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            <div class="col-md-12 d-flex gap-2">
                <a href="{{ route('leads.closed') }}" class="btn btn-sm btn-light border">
                    Reset
                </a>

                <a href="{{ route('leads.closed', array_merge(request()->query(), ['export' => 'excel'])) }}"
                   class="btn btn-sm btn-success">
                    <i class="las la-file-excel"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Closed Lead List</h4>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Lead No</th>
                    <th>Student / Parent</th>
                    <th>Phone</th>
                    <th>Level</th>
                    <th>Service</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Fee</th>
                    <th>User</th>
                    <th>Closed Date</th>
                    <th>Closed By</th>
                    <th>Closed Note</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leads as $lead)
                    @php
                        $quotation = $lead->quotations?->sortByDesc('created_at')->first();

                        $fee = $quotation?->final_amount
                            ?? $quotation?->final_fee
                            ?? $quotation?->fee_amount
                            ?? $quotation?->total_fee
                            ?? null;
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $lead->lead_no }}</strong>
                        </td>

                        <td>
                            {{ $lead->student_name ?? '-' }}<br>
                            <small class="text-muted">{{ $lead->parent_name ?? '-' }}</small>
                        </td>

                        <td>{{ $lead->phone ?? $lead->mobile ?? '-' }}</td>

                        <td>{{ $lead->standard?->name ?? '-' }}</td>

                        <td>{{ $lead->course?->name ?? '-' }}</td>

                        <td>{{ $lead->source?->name ?? '-' }}</td>

                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                @if($lead->closedStatus)
    <span class="badge"
          style="background-color: {{ $lead->closedStatus->color }}; color: #fff;">
        {{ $lead->closedStatus->name }}
    </span>
@else
    -
@endif
                            </span>
                        </td>

                        <td>{{ $lead->priority?->name ?? '-' }}</td>

                        <td>
                            @if($fee)
                                ₹{{ number_format((float) $fee, 2) }}
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $lead->assignedUser?->name ?? '-' }}</td>

                        <td>{{ $lead->closed_at?->format('d M Y h:i A') ?? '-' }}</td>

                        <td>{{ $lead->closedBy?->name ?? '-' }}</td>

                        <td>{{ \Illuminate\Support\Str::limit($lead->closed_note, 60) }}</td>
                        <td>
    <button type="button"
            class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#followupHistory{{ $lead->id }}">
        View
    </button>
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center text-muted py-4">
                            No closed leads found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $leads->links() }}
    </div>
</div>

@php
    $followupOrdinal = function ($number) {
        $number = (int) $number;

        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return $number . 'th';
        }

        return $number . match ($number % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    };
@endphp

@foreach($leads as $lead)
    <div class="modal fade" id="followupHistory{{ $lead->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Follow-up History - {{ $lead->lead_no }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Parent Name</small>
                                    <strong>{{ $lead->parent_name ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Student Name</small>
                                    <strong>{{ $lead->student_name ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Phone</small>
                                    <strong>{{ $lead->phone ?? $lead->mobile ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Level</small>
                                    <strong>{{ $lead->standard?->name ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Service</small>
                                    <strong>{{ $lead->course?->name ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Closed Status</small>
                                    @if($lead->closedStatus)
                                        <span class="badge"
                                              style="background-color: {{ $lead->closedStatus->color }}; color: #fff;">
                                            {{ $lead->closedStatus->name }}
                                        </span>
                                    @else
                                        <strong>-</strong>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Closed Date</small>
                                    <strong>{{ $lead->closed_at?->format('d M Y h:i A') ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Closed By</small>
                                    <strong>{{ $lead->closedBy?->name ?? '-' }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Assigned User</small>
                                    <strong>{{ $lead->assignedUser?->name ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Follow-up Date</th>
                                    <th>Next Follow-up Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Note</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($lead->followups as $history)
                                    @php
                                        $historyNumber = $lead->followups->count() - $loop->index;

                                        $historyColor = $history->status == 'completed'
                                            ? 'success'
                                            : ($history->followup_at < now() ? 'danger' : 'warning');
                                    @endphp

                                    <tr>
                                        <td>{{ $followupOrdinal($historyNumber) }}</td>

                                        <td>
                                            {{ $history->followup_at?->format('d M Y h:i A') ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $history->next_followup_at?->format('d M Y h:i A') ?? $history->followup_at?->format('d M Y h:i A') ?? '-' }}
                                        </td>

                                        <td>{{ $history->type?->name ?? '-' }}</td>

                                        <td>
                                            <span class="badge bg-{{ $historyColor }}-subtle text-{{ $historyColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $history->assignedUser?->name ?? $lead->assignedUser?->name ?? '-' }}
                                        </td>

                                        <td>{{ $history->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            No follow-up history found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection


