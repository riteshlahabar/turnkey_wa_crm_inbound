@extends('layouts.app')
@section('title','Lead Details')
@section('content')
@include('crm._page_header', ['title' => 'Lead Details', 'subtitle' => $lead->lead_no])
<div class="row">
    <div class="col-lg-8">
        <div class="card"><div class="card-header d-flex justify-content-between"><h4 class="card-title mb-0">Student / Parent Details</h4><a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-primary">Edit</a></div><div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><small class="text-muted">Student</small><h6>{{ $lead->student_name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Parent</small><h6>{{ $lead->parent_name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Phone</small><h6>{{ $lead->phone }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Level</small><h6>{{ $lead->standard?->name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Service</small><h6>{{ $lead->course?->name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Assigned User</small><h6>{{ $lead->assignedUser?->name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Source</small><h6>{{ $lead->source?->name ?? '-' }}</h6></div>
                <div class="col-md-4"><small class="text-muted">Status</small><h6><span class="badge bg-{{ $lead->status?->color ?? 'secondary' }}-subtle text-{{ $lead->status?->color ?? 'secondary' }}">{{ $lead->status?->name ?? '-' }}</span></h6></div>
                <div class="col-md-4"><small class="text-muted">Priority</small><h6><span class="badge bg-{{ $lead->priority?->color ?? 'secondary' }}-subtle text-{{ $lead->priority?->color ?? 'secondary' }}">{{ $lead->priority?->name ?? '-' }}</span></h6></div>
                <div class="col-12"><small class="text-muted">Note</small><p>{{ $lead->note ?? '-' }}</p></div>
            </div>
            @if($lead->status?->is_admission)<a href="{{ route('leads.admission-form', $lead) }}" target="_blank" class="btn btn-success"><i class="iconoir-printing-page me-1"></i>Print Admission Form</a>@endif
        </div></div>

        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Follow-up History</h4></div><div class="card-body table-responsive"><table class="table"><thead><tr><th>Date</th><th>Type</th><th>Status</th><th>Note</th><th>User</th></tr></thead><tbody>@forelse($lead->followups as $f)<tr><td>{{ $f->followup_at?->format('d M Y h:i A') }}</td><td>{{ $f->type?->name ?? '-' }}</td><td>{{ ucfirst(str_replace('_',' ', $f->status)) }}</td><td>{{ $f->note }}</td><td>{{ $f->creator?->name }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted">No follow-ups.</td></tr>@endforelse</tbody></table></div></div>

        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Fee Quotations</h4></div><div class="card-body table-responsive"><table class="table"><thead><tr><th>No</th><th>Service</th><th>Total</th><th>Discount</th><th>Final</th><th>Print</th></tr></thead><tbody>@forelse($lead->quotations as $q)<tr><td>{{ $q->quotation_no }}</td><td>{{ $q->course?->name }}</td><td>{{ number_format($q->total_fee,2) }}</td><td>{{ number_format($q->discount,2) }}</td><td>{{ number_format($q->final_fee,2) }}</td><td><a href="{{ route('leads.quotation.print', $q) }}" target="_blank" class="btn btn-sm btn-light border">Print</a></td></tr>@empty<tr><td colspan="6" class="text-center text-muted">No quotations.</td></tr>@endforelse</tbody></table></div></div>
    </div>

    <div class="col-lg-4">
        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Add Follow-up</h4></div><div class="card-body"><form method="POST" action="{{ route('followups.store', $lead) }}">@csrf
            <div class="mb-2"><label class="form-label">Type</label><select name="followup_type_id" class="form-select"><option value="">Select</option>@foreach($followupTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
            <div class="row g-2"><div class="col-6"><label class="form-label">Date</label><input type="date" name="followup_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div><div class="col-6"><label class="form-label">Time</label><input type="time" name="followup_time" class="form-control" value="{{ now()->format('H:i') }}" required></div></div>
            <div class="mb-2 mt-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="pending">Pending</option><option value="completed">Completed</option><option value="no_response">No Response</option><option value="cancelled">Cancelled</option></select></div>
            <div class="mb-2"><label class="form-label">Note</label><textarea name="note" class="form-control" rows="3"></textarea></div>
            <div class="row g-2"><div class="col-6"><label class="form-label">Next Date</label><input type="date" name="next_followup_date" class="form-control"></div><div class="col-6"><label class="form-label">Next Time</label><input type="time" name="next_followup_time" class="form-control"></div></div>
            <button class="btn btn-primary mt-3 w-100">Save Follow-up</button>
        </form></div></div>

        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Create Fee Quotation</h4></div><div class="card-body"><form method="POST" action="{{ route('leads.quotation.store', $lead) }}">@csrf
            <div class="mb-2"><label class="form-label">Service</label><select name="course_id" class="form-select"><option value="">Select</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected($lead->course_id == $course->id)>{{ $course->name }}</option>@endforeach</select></div>
            <div class="mb-2"><label class="form-label">Total Fee</label><input type="number" step="0.01" name="total_fee" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Discount</label><input type="number" step="0.01" name="discount" class="form-control" value="0"></div>
            <div class="mb-2"><label class="form-label">Installment Note</label><textarea name="installment_note" class="form-control" rows="2"></textarea></div>
            <button class="btn btn-success w-100">Save Quotation</button>
        </form></div></div>

        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Timeline</h4></div><div class="card-body">@forelse($lead->activities as $activity)<div class="border-start ps-3 mb-3"><strong>{{ $activity->description }}</strong><br><small class="text-muted">{{ $activity->created_at->format('d M Y h:i A') }} by {{ $activity->user?->name ?? '-' }}</small></div>@empty<p class="text-muted">No activity.</p>@endforelse</div></div>
    </div>
</div>
@endsection
