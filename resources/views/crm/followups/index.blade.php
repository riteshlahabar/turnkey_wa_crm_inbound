@extends('layouts.app')

@section('title','Follow-ups')

@section('content')
@include('crm._page_header', [
    'title' => 'Follow-ups',
    'subtitle' => 'Latest follow-up list with full lead information'
])

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

<ul class="nav nav-pills mb-3">
    @foreach(['today'=>'Today','pending'=>'Pending','overdue'=>'Overdue','completed'=>'Completed','all'=>'All'] as $key => $label)
        <li class="nav-item">
            <a href="{{ route('followups.index', array_merge(request()->except('page'), ['tab' => $key])) }}"
               class="nav-link {{ $tab == $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('followups.index') }}" class="row g-2">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="col-md-3">
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Search name, mobile, level, course">
            </div>

            <div class="col-md-2">
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="type_id" class="form-select">
                    <option value="">All Types</option>
                    @foreach($followupTypes as $type)
                        <option value="{{ $type->id }}" @selected(request('type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="standard_id" class="form-select">
                    <option value="">All Levels</option>
                    @foreach($standards as $standard)
                        <option value="{{ $standard->id }}" @selected(request('standard_id') == $standard->id)>
                            {{ $standard->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="course_id" class="form-select">
                    <option value="">All Services</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            <div class="col-md-12">
                <a href="{{ route('followups.index', ['tab' => $tab]) }}" class="btn btn-sm btn-light border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Latest Follow-up</th>
                    <th>Follow-up Times</th>
                    <th>Lead No</th>
                    <th>Parent Name</th>
                    <th>Student Name</th>
                    <th>Phone</th>
                    <th>Level</th>
                    <th>Service</th>
                    <th>Lead Status</th>
                    <th>Fee</th>
                    <th>Next Date</th>
                    <th>Type</th>
                    <th>Follow-up Status</th>
                    <th>User</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($followups as $f)
                    @php
                        $followupCount = (int) ($f->followup_times ?? 1);

                        $badgeColor = $f->status == 'completed'
                            ? 'success'
                            : ($f->followup_at < now() ? 'danger' : 'warning');

                        $latestQuotation = $f->lead?->quotations
                            ? $f->lead->quotations->sortByDesc('created_at')->first()
                            : null;

                        $feeAmount = $latestQuotation?->final_amount
                            ?? $latestQuotation?->final_fee
                            ?? $latestQuotation?->fee_amount
                            ?? $latestQuotation?->total_fee
                            ?? null;
                            
                         $hasPendingFollowup = $f->lead?->followups
        ? $f->lead->followups->where('status', 'pending')->count() > 0
        : false;    
                    @endphp

                    <tr>
                        <td>{{ $f->followup_at?->format('d M Y h:i A') }}</td>

                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#followupHistory{{ $f->lead_id }}">
                                {{ $followupOrdinal($followupCount) }}
                            </button>
                        </td>

                        <td>{{ $f->lead?->lead_no ?? '-' }}</td>

                        <td>{{ $f->lead?->parent_name ?? '-' }}</td>

                        <td>{{ $f->lead?->student_name ?? '-' }}</td>

                        <td>{{ $f->lead?->phone ?? $f->lead?->mobile ?? '-' }}</td>

                        <td>{{ $f->lead?->standard?->name ?? '-' }}</td>

                        <td>{{ $f->lead?->course?->name ?? '-' }}</td>

                        <td>{{ $f->lead?->status?->name ?? '-' }}</td>

                        <td>
                            @if($feeAmount)
                                ₹{{ number_format((float) $feeAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $f->lead?->next_followup_at?->format('d M Y h:i A') ?? '-' }}</td>

                        <td>{{ $f->type?->name ?? '-' }}</td>

                        <td>
                            <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }}">
                                {{ ucfirst(str_replace('_', ' ', $f->status)) }}
                            </span>
                        </td>

                        <td>{{ $f->assignedUser?->name ?? $f->lead?->assignedUser?->name ?? '-' }}</td>

                        <td>{{ \Illuminate\Support\Str::limit($f->note, 50) }}</td>

                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    ⋮
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($f->status != 'completed')
    <li>
        <button type="button"
                class="dropdown-item"
                data-bs-toggle="modal"
                data-bs-target="#complete{{ $f->id }}">
            Complete this follow-up
        </button>
    </li>
@else
    @if(!$hasPendingFollowup)
        <li>
            <button type="button"
                    class="dropdown-item"
                    data-bs-toggle="modal"
                    data-bs-target="#nextFollowup{{ $f->id }}">
                Create Next Follow-up
            </button>
        </li>
    @else
        <li>
            <span class="dropdown-item text-muted">
                Pending follow-up already exists
            </span>
        </li>
    @endif
@endif

                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#followupHistory{{ $f->lead_id }}">
                                            All follow-ups list
                                        </button>
                                    </li>
                                    
                                    <li>
    <button type="button"
            class="dropdown-item"
            data-bs-toggle="modal"
            data-bs-target="#changeType{{ $f->id }}">
        Change Type
    </button>
</li>

                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#closeLead{{ $f->id }}">
                                            Closed leads
                                        </button>
                                    </li>

                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#quotation{{ $f->id }}">
                                            Create fee quotation
                                        </button>
                                    </li>
                                    
                                    
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center text-muted py-4">
                            No follow-ups found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $followups->links() }}
    </div>
</div>

@foreach($followups as $f)
    @php
        $leadFollowups = $f->lead?->followups ?? collect();
    @endphp

    <div class="modal fade" id="followupHistory{{ $f->lead_id }}" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        All Follow-ups - {{ $f->lead?->lead_no }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="card border mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted d-block">Parent Name</small>
                <strong>{{ $f->lead?->parent_name ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Student Name</small>
                <strong>{{ $f->lead?->student_name ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Phone</small>
                <strong>{{ $f->lead?->phone ?? $f->lead?->mobile ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Level</small>
                <strong>{{ $f->lead?->standard?->name ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Service</small>
                <strong>{{ $f->lead?->course?->name ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Lead Status</small>
                <strong>{{ $f->lead?->status?->name ?? '-' }}</strong>
            </div>

            <div class="col-md-4">
                <small class="text-muted d-block">Next Follow-up Date</small>
                <strong>{{ $f->lead?->next_followup_at?->format('d M Y h:i A') ?? '-' }}</strong>
            </div>
        </div>
    </div>
</div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date/Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Note</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($leadFollowups as $history)
                                    @php
                                        $historyNumber = $leadFollowups->count() - $loop->index;

                                        $historyColor = $history->status == 'completed'
                                            ? 'success'
                                            : ($history->followup_at < now() ? 'danger' : 'warning');
                                    @endphp

                                    <tr>
                                        <td>{{ $followupOrdinal($historyNumber) }}</td>
                                        <td>{{ $history->followup_at?->format('d M Y h:i A') }}</td>
                                        <td>{{ $history->type?->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $historyColor }}-subtle text-{{ $historyColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $history->assignedUser?->name ?? $history->lead?->assignedUser?->name ?? '-' }}</td>
                                        <td>{{ $history->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No history found.
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

    @if($f->status != 'completed')
    <div class="modal fade" id="complete{{ $f->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('followups.complete', $f) }}" class="modal-content">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title">Complete Follow-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning mb-0">
                        Are you sure you want to complete this follow-up?
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button class="btn btn-success">
                        Yes, Complete
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

    <div class="modal fade" id="closeLead{{ $f->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('followups.closeLead', $f) }}" class="modal-content">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title">Closed Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Select Final Lead Status</label>
                    <select name="closed_status_id" class="form-select mb-3" required>
    <option value="">Select Closed Status</option>

    @foreach($closedStatuses as $closedStatus)
        <option value="{{ $closedStatus->id }}">
            {{ $closedStatus->name }}
        </option>
    @endforeach
</select>

<div class="mt-2">
    @foreach($closedStatuses as $closedStatus)
        <span class="badge me-1"
              style="background-color: {{ $closedStatus->color }}; color: #fff;">
            {{ $closedStatus->name }}
        </span>
    @endforeach
</div>

                    <label class="form-label">Closing Note</label>
                    <textarea name="note" class="form-control" placeholder="Enter closing note"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">Close Lead Completely</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="quotation{{ $f->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('leads.quotation.store', $f->lead_id) }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Create Fee Quotation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Service</label>
                    <select name="course_id" class="form-select mb-3">
                        <option value="">Select Service</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected($f->lead?->course_id == $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>

                    <label class="form-label">Total Fee</label>
                    <input type="number" step="0.01" name="total_fee" class="form-control mb-3" required>

                    <label class="form-label">Discount</label>
                    <input type="number" step="0.01" name="discount" class="form-control mb-3" value="0">

                    <label class="form-label">Installment / Note</label>
                    <textarea name="installment_note" class="form-control" placeholder="Installment or quotation note"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="changeType{{ $f->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('followups.changeType', $f) }}" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Change Follow-up Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Follow-up Type</label>
                <select name="followup_type_id" class="form-select" required>
                    <option value="">Select Type</option>
                    @foreach($followupTypes as $type)
                        <option value="{{ $type->id }}" @selected($f->followup_type_id == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Update Type</button>
            </div>
        </form>
    </div>
</div>

@if($f->status == 'completed')
    @php
        $hasPendingFollowup = $f->lead?->followups
            ? $f->lead->followups->where('status', 'pending')->count() > 0
            : false;
    @endphp

    @if(!$hasPendingFollowup)
        <div class="modal fade" id="nextFollowup{{ $f->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('followups.createNextFollowup', $f) }}" class="modal-content">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Create Next Follow-up</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Follow-up Type</label>
                        <select name="followup_type_id" class="form-select mb-3">
                            <option value="">Same as current type</option>
                            @foreach($followupTypes as $type)
                                <option value="{{ $type->id }}" @selected($f->followup_type_id == $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="date" name="next_followup_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Next Follow-up Time</label>
                                <input type="time" name="next_followup_time" class="form-control" required>
                            </div>
                        </div>

                        <label class="form-label mt-3">Note</label>
                        <textarea name="note" class="form-control" placeholder="Enter note for next follow-up"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            Create Next Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
@endforeach

@endsection