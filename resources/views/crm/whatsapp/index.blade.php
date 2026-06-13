@extends('layouts.app')

@section('title', 'WhatsApp Records')

@section('content')
@include('crm._page_header', [
    'title' => 'WhatsApp Records',
    'subtitle' => 'Records sent from mobile app only.'
])

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">All WhatsApp Records</h4>

        <form method="GET" class="d-flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Search phone, parent or message">

            <button class="btn btn-primary">
                Search
            </button>
        </form>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Phone</th>
                    <th>Parent</th>
                    <th>Message</th>
                    <th>Sent At</th>
                    <th>Lead</th>
                    <th width="110">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs ?? [] as $log)
                    <tr>
                        <td>
                            <strong>{{ $log->phone }}</strong>
                        </td>

                        <td>
                            {{ $log->parent_name ?? '-' }}
                        </td>

                        <td>
                            {{ \Illuminate\Support\Str::limit($log->message ?? '-', 70) }}
                        </td>

                        <td>
                            {{ optional($log->sent_at)->format('d M Y h:i A') ?? '-' }}
                        </td>

                        <td>
                            @if($log->lead)
                                <a href="{{ route('leads.show', $log->lead) }}"
                                   class="badge bg-success-subtle text-success">
                                    {{ $log->lead->lead_no }}
                                </a>
                            @else
                                <span class="badge bg-warning-subtle text-warning">
                                    Not Converted
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                @if(!$log->lead)
                                    <form method="POST" action="{{ route('leads.convert.whatsapp', $log) }}">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"
                                                title="Convert to Lead">
                                            <i class="iconoir-user-plus"></i>
                                        </button>
                                    </form>
                                @endif

                                <form method="POST"
                                      action="{{ route('whatsapp.destroy', $log) }}"
                                      onsubmit="return confirm('Delete this WhatsApp record?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-light border text-danger"
                                            title="Delete">
                                        <i class="las la-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No WhatsApp records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(isset($logs) && method_exists($logs, 'links'))
            {{ $logs->links() }}
        @endif
    </div>
</div>
@endsection
