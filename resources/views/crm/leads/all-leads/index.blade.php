@extends('layouts.app')

@section('title','All Leads')

@section('content')
@include('crm._page_header', [
    'title' => 'All Leads',
    'subtitle' => 'All leads from call, WhatsApp, walk-in, app, website, and reference sources'
])

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Lead no/name/phone">
            </div>

            <div class="col-md-2">
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
                <select name="source_id" class="form-select">
                    <option value="">All Sources</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->id }}" @selected(request('source_id') == $source->id)>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="assigned_user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>

                <a href="{{ route('leads.create') }}" class="btn btn-success">
                    <i class="iconoir-plus-circle me-1"></i>Add Lead
                </a>
            </div>
        </form>
    </div>
</div>

<form id="bulkLeadForm" method="POST" action="{{ route('leads.bulk') }}">
    @csrf

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Lead List</h4>

            <div class="d-flex gap-2 align-items-center">
    <input type="hidden" name="bulk_action" id="bulkActionValue">

    <button type="submit"
            id="exportCsvBtn"
            class="btn btn-sm btn-outline-success"
            title="Export CSV">
        <i class="las la-file-csv"></i>
    </button>

    <select id="bulkActionSelect" class="form-select form-select-sm" style="width:190px">
        <option value="">Select Bulk Action</option>
        <option value="assign">Assign User</option>
        <option value="status">Change Status</option>
        <option value="priority">Change Priority</option>
    </select>

    <select name="assigned_user_id" id="bulkAssignedUser" class="form-select form-select-sm" disabled>
        <option value="">User</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>

    <select name="lead_status_id" id="bulkLeadStatus" class="form-select form-select-sm" disabled>
        <option value="">Status</option>
        @foreach($statuses as $status)
            <option value="{{ $status->id }}">{{ $status->name }}</option>
        @endforeach
    </select>

    <select name="lead_priority_id" id="bulkLeadPriority" class="form-select form-select-sm" disabled>
        <option value="">Priority</option>
        @foreach($priorities as $priority)
            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
        @endforeach
    </select>

    <button type="submit" id="applyBulkBtn" class="btn btn-sm btn-primary">
        Apply
    </button>
</div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" onclick="document.querySelectorAll('.lead-check').forEach(cb => cb.checked = this.checked)">
                        </th>
                        <th>Lead No</th>
                        <th>Student / Parent</th>
                        <th>Phone</th>
                        <th>Level</th>
                        <th>Service</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Next Follow-up</th>
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leads as $lead)
                        @php
                            $statusItem = $lead->status;

                            if (!$statusItem && !empty($lead->lead_status_id)) {
                                $statusItem = $statuses->first(function ($item) use ($lead) {
                                    return (int) $item->id === (int) $lead->lead_status_id;
                                });
                            }

                            $priorityItem = $lead->priority;

                            if (!$priorityItem && !empty($lead->lead_priority_id)) {
                                $priorityItem = $priorities->first(function ($item) use ($lead) {
                                    return (int) $item->id === (int) $lead->lead_priority_id;
                                });
                            }

                            $statusColor = $statusItem?->color ?: 'secondary';
                            $priorityColor = $priorityItem?->color ?: 'secondary';

                            $statusIsHex = str_starts_with($statusColor, '#');
                            $priorityIsHex = str_starts_with($priorityColor, '#');
                        @endphp

                        <tr>
                            <td>
                                <input class="lead-check" type="checkbox" name="lead_ids[]" value="{{ $lead->id }}">
                            </td>

                            <td>
                                <a href="{{ route('leads.show', $lead) }}">
                                    <strong>{{ $lead->lead_no }}</strong>
                                </a>
                            </td>

                            <td>
                                {{ $lead->student_name ?? '-' }}<br>
                                <small class="text-muted">{{ $lead->parent_name }}</small>
                            </td>

                            <td>{{ $lead->phone }}</td>

                            <td>{{ $lead->standard?->name ?? '-' }}</td>

                            <td>{{ $lead->course?->name ?? '-' }}</td>

                            <td>{{ $lead->source?->name ?? '-' }}</td>

                            <td>
                                @if($statusIsHex)
                                    <span class="badge" style="background-color: {{ $statusColor }}; color: #fff;">
                                        {{ $statusItem?->name ?? '-' }}
                                    </span>
                                @else
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">
                                        {{ $statusItem?->name ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($priorityIsHex)
                                    <span class="badge" style="background-color: {{ $priorityColor }}; color: #fff;">
                                        {{ $priorityItem?->name ?? '-' }}
                                    </span>
                                @else
                                    <span class="badge bg-{{ $priorityColor }}-subtle text-{{ $priorityColor }}">
                                        {{ $priorityItem?->name ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            <td>{{ $lead->next_followup_at?->format('d M Y h:i A') ?? '-' }}</td>

                            <td>{{ $lead->assignedUser?->name ?? '-' }}</td>

                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-light border">
                                        <i class="las la-eye"></i>
                                    </a>

                                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-light border">
                                        <i class="las la-edit"></i>
                                    </a>

                                    <button type="submit"
                                            form="deleteLeadForm{{ $lead->id }}"
                                            class="btn btn-sm btn-light border text-danger"
                                            onclick="return confirm('Delete lead?')">
                                        <i class="las la-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $leads->links() }}
        </div>
    </div>
</form>

@foreach($leads as $lead)
    <form id="deleteLeadForm{{ $lead->id }}" method="POST" action="{{ route('leads.destroy', $lead) }}" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('bulkLeadForm');
        const bulkActionSelect = document.getElementById('bulkActionSelect');
        const bulkActionValue = document.getElementById('bulkActionValue');

        const assignedUser = document.getElementById('bulkAssignedUser');
        const leadStatus = document.getElementById('bulkLeadStatus');
        const leadPriority = document.getElementById('bulkLeadPriority');

        function resetDropdowns() {
            assignedUser.disabled = true;
            leadStatus.disabled = true;
            leadPriority.disabled = true;

            assignedUser.value = '';
            leadStatus.value = '';
            leadPriority.value = '';
        }

        function toggleDropdowns() {
            const action = bulkActionSelect.value;

            bulkActionValue.value = action;
            resetDropdowns();

            if (action === 'assign') {
                assignedUser.disabled = false;
            }

            if (action === 'status') {
                leadStatus.disabled = false;
            }

            if (action === 'priority') {
                leadPriority.disabled = false;
            }
        }

        bulkActionSelect.addEventListener('change', toggleDropdowns);

        form.addEventListener('submit', function (e) {
            const submitter = e.submitter;

            if (submitter && submitter.id === 'exportCsvBtn') {
                bulkActionValue.value = 'export_csv';
                return;
            }

            bulkActionValue.value = bulkActionSelect.value;
        });

        resetDropdowns();
    });
</script>

@endsection