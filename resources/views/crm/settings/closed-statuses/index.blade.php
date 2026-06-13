@extends('layouts.app')

@section('title','Closed Lead Statuses')

@section('content')
@include('crm._page_header', [
    'title' => 'Closed Lead Statuses',
    'subtitle' => 'Manage statuses used when closing leads'
])

<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('settings.closed-statuses.store') }}" class="row g-2 align-items-end">
            @csrf

            <div class="col-md-5">
                <label class="form-label">Status Name</label>
                <input type="text" name="name" class="form-control" placeholder="Admission Done" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Color</label>
                <input type="color" name="color" class="form-control form-control-color" value="#198754" required>
            </div>

            <div class="col-md-2">
                <div class="form-check mt-4">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Add Status</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Status</th>
                    <th>Color</th>
                    <th>Active</th>
                    <th width="220">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($statuses as $status)
                    <tr>
                        <td>
                            <span class="badge"
                                  style="background-color: {{ $status->color }}; color: #fff;">
                                {{ $status->name }}
                            </span>
                        </td>

                        <td>
                            <span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:{{ $status->color 
}};"></span>
                            {{ $status->color }}
                        </td>

                        <td>
                            @if($status->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editStatus{{ $status->id }}">
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('settings.closed-statuses.destroy', $status) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this closed status?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editStatus{{ $status->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST"
                                  action="{{ route('settings.closed-statuses.update', $status) }}"
                                  class="modal-content">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Closed Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <label class="form-label">Status Name</label>
                                    <input type="text" name="name" class="form-control mb-3" value="{{ $status->name }}" required>

                                    <label class="form-label">Color</label>
                                    <input type="color" name="color" class="form-control form-control-color mb-3" value="{{ 
$status->color }}" required>

                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" 
@checked($status->is_active)>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-primary">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No closed statuses found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $statuses->links() }}
    </div>
</div>
@endsection
