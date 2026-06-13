@extends('layouts.app')
@section('title','Pending Follow-up Report')
@section('content')
@include('crm._page_header', ['title' => 'Pending Follow-up Report', 'subtitle' => 'Pending and upcoming follow-ups'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Date</th><th>Lead</th><th>Phone</th><th>Type</th><th>User</th><th>Status</th></tr></thead><tbody>@foreach($followups as $f)<tr><td>{{ $f->followup_at?->format('d M Y h:i A') }}</td><td><a href="{{ route('leads.show',$f->lead_id) }}">{{ $f->lead?->lead_no }}</a></td><td>{{ $f->lead?->phone }}</td><td>{{ $f->type?->name }}</td><td>{{ $f->lead?->assignedUser?->name }}</td><td>{{ ucfirst($f->status) }}</td></tr>@endforeach</tbody></table>{{ $followups->links() }}</div></div>
@endsection
