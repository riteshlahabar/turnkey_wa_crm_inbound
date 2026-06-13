@extends('layouts.app')

@section('title','Calls')

@section('content')

<style>
    .calls-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
    }

    .calls-table th {
        font-size: 13px;
        font-weight: 700;
        color: #344054;
        background: #f8fafc;
        padding: 14px 16px;
        white-space: nowrap;
    }

    .calls-table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
        color: #1f2937;
        border-bottom: 1px solid #eef2f7;
    }

    .calls-table tbody tr:hover {
        background: #f9fafb;
    }

    .action-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        padding: 0;
    }

    .action-btn i {
        font-size: 17px;
        line-height: 1;
    }

    .btn-whatsapp {
        background: #00a884;
        color: #ffffff;
    }

    .btn-whatsapp:hover {
        background: #008f72;
        color: #ffffff;
    }

    .btn-delete {
        background: #ef5b3f;
        color: #ffffff;
    }

    .btn-delete:hover {
        background: #dc4428;
        color: #ffffff;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 7px 12px;
        border-radius: 7px;
    }

    .followup-badge {
        min-width: 34px;
        padding: 7px 10px;
        border-radius: 7px;
        font-weight: 700;
    }

    .custom-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .pagination-info {
        font-size: 13px;
        color: #667085;
    }

    .pagination-box {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-box a,
    .pagination-box span {
        min-width: 34px;
        height: 34px;
        padding: 0 11px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        text-decoration: none;
        background: #ffffff;
        color: #344054;
    }

    .pagination-box a:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .pagination-box .active-page {
        background: #7367f0;
        border-color: #7367f0;
        color: #ffffff;
    }

    .pagination-box .disabled-page {
        color: #98a2b3;
        background: #f9fafb;
        cursor: not-allowed;
    }
</style>

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
            <div>
                <h4 class="page-title mb-1">Calls</h4>
            </div>
        </div>
    </div>
</div>

<div class="card calls-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">All Calls</h4>

        <span class="badge bg-primary px-3 py-2">
            {{ $calls->total() }} total
        </span>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table calls-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Phone</th>
                        <th>Name</th>
                        <th>Call Time</th>
                        <th>Current Status</th>
                        <th>Followups</th>
                        <th width="115">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($calls as $call)
                        <tr>
                            <td>
                                <strong>{{ $call->phone }}</strong>
                            </td>

                            <td>
                                @if($call->name)
                                    {{ $call->name }}
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>

                            <td>
                                {{ $call->created_at->format('d M Y, h:i A') }}
                            </td>

                            <td>
                                @if($call->latestFollowup)
                                    <span class="badge bg-success-subtle text-success status-badge">
                                        {{ $call->latestFollowup->status->title ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning status-badge">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info followup-badge">
                                    {{ optional($call->followups)->count() ?? 0 }}
                                </span>
                            </td>

                            <td>
                                <div class="action-wrapper">

                                    @if($call->latestFollowup)
                                        <form method="POST"
                                              action="{{ route('whatsapp.sendForCall', $call->id) }}">
                                            @csrf

                                            <input type="hidden"
                                                   name="status_id"
                                                   value="{{ $call->latestFollowup->status_id }}">

                                            <button type="submit"
                                                    class="action-btn btn-whatsapp"
                                                    title="Send WhatsApp">
                                                <i class="lab la-whatsapp"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('calls.destroy', $call->id) }}"
                                          onsubmit="return confirm('Delete this call?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-light text-danger border action-btn"
                                                title="Delete Call">
                                            <i class="las la-trash-alt"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                No calls found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($calls->hasPages())
            <div class="custom-pagination">

                <div class="pagination-info">
                    Showing {{ $calls->firstItem() }} to {{ $calls->lastItem() }}
                    of {{ $calls->total() }} results
                </div>

                <div class="pagination-box">

                    @if($calls->onFirstPage())
                        <span class="disabled-page">Previous</span>
                    @else
                        <a href="{{ $calls->previousPageUrl() }}">Previous</a>
                    @endif

                    @for($i = 1; $i <= $calls->lastPage(); $i++)
                        @if($i == $calls->currentPage())
                            <span class="active-page">{{ $i }}</span>
                        @else
                            <a href="{{ $calls->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($calls->hasMorePages())
                        <a href="{{ $calls->nextPageUrl() }}">Next</a>
                    @else
                        <span class="disabled-page">Next</span>
                    @endif

                </div>

            </div>
        @endif

    </div>
</div>

@endsection