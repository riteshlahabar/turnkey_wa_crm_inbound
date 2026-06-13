@extends('layouts.app')
@section('title','Lead Report')
@section('content')
@include('crm._page_header', ['title' => 'Lead Report', 'subtitle' => 'Complete lead listing report'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Lead No</th><th>Student</th><th>Phone</th><th>Source</th><th>Status</th><th>Priority</th><th>User</th><th>Date</th></tr></thead><tbody>@foreach($leads as $lead)<tr><td><a href="{{ route('leads.show',$lead) }}">{{ $lead->lead_no }}</a></td><td>{{ $lead->student_name ?? $lead->parent_name }}</td><td>{{ $lead->phone }}</td><td>{{ $lead->source?->name }}</td><td>{{ $lead->status?->name }}</td><td>{{ $lead->priority?->name }}</td><td>{{ $lead->assignedUser?->name }}</td><td>{{ $lead->created_at->format('d M Y') }}</td></tr>@endforeach</tbody></table>{{ $leads->links() }}</div></div>
@endsection
