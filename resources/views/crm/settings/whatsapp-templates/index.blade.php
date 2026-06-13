@extends('layouts.app')

@section('title', 'WhatsApp Templates')

@section('content')
@include('crm._page_header', [
    'title' => 'WhatsApp Templates',
    'subtitle' => 'Create service-wise WhatsApp messages used by the mobile app.'
])

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    
                    Add WhatsApp Template
                </h4>
                <small class="text-muted">Select course and write message template.</small>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('settings.whatsapp-templates.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Services / General Template</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Title</label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               class="form-control"
                               placeholder="Example: Fee details message">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message"
                                  rows="8"
                                  class="form-control"
                                  required
                                  placeholder="Write WhatsApp message here...">{{ old('message') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="iconoir-save-action-floppy me-1"></i>
                        Save Template
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="mb-2">Suggested Variables</h6>
                <p class="text-muted mb-2">You can type these words in template. Mobile app can replace them later if required.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border">{parent_name}</span>
                    <span class="badge bg-light text-dark border">{student_name}</span>
                    <span class="badge bg-light text-dark border">{course_name}</span>
                    <span class="badge bg-light text-dark border">{standard}</span>
                    <span class="badge bg-light text-dark border">{academy_name}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Template List</h4>
                    <small class="text-muted">Service-wise WhatsApp templates</small>
                </div>

                <form method="GET" class="d-flex gap-2">
                    <select name="course_id" class="form-select">
                        <option value="">All Services</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-light border">Filter</button>
                </form>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Service</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th width="170">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    @if($template->course)
                                        <span class="badge bg-primary-subtle text-primary">{{ $template->course->name }}</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info">General</span>
                                    @endif
                                </td>
                                <td>{{ $template->title ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($template->message, 90) }}</td>
                                <td>{{ $template->sort_order }}</td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-light border"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTemplateModal{{ $template->id }}">
                                        <i class="iconoir-edit-pencil me-1"></i>Edit
                                    </button>

                                    <form method="POST"
                                          action="{{ route('settings.whatsapp-templates.destroy', $template) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this WhatsApp template?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger">
                                            <i class="iconoir-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editTemplateModal{{ $template->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('settings.whatsapp-templates.update', $template) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit WhatsApp Template</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Service</label>
                                                        <select name="course_id" class="form-select">
                                                            <option value="">All Services / General Template</option>
                                                            @foreach($courses as $course)
                                                                <option value="{{ $course->id }}" @selected($template->course_id == $course->id)>
                                                                    {{ $course->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Template Title</label>
                                                        <input type="text" name="title" value="{{ $template->title }}" class="form-control">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                                        <textarea name="message" rows="8" class="form-control" required>{{ $template->message }}</textarea>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Sort Order</label>
                                                        <input type="number" name="sort_order" value="{{ $template->sort_order }}" class="form-control" min="0">
                                                    </div>

                                                    <div class="col-md-6 mb-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                   name="is_active"
                                                                   value="1"
                                                                   class="form-check-input"
                                                                   id="activeTemplate{{ $template->id }}"
                                                                   @checked($template->is_active)>
                                                            <label class="form-check-label" for="activeTemplate{{ $template->id }}">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Template</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No WhatsApp templates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
