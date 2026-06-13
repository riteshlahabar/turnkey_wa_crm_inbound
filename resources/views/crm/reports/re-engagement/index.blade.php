@extends('layouts.app')
@section('title','Re-engagement Report')
@section('content')
@include('crm._page_header', ['title' => 'Re-engagement Report', 'subtitle' => 'Leads with no activity for 7 days'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Lead No</th><th>Name</th><th>Phone</th><th>Status</th><th>User</th><th>Last Activity</th></tr></thead><tbody>@foreach($leads as $lead)<tr><td><a href="{{ route('leads.show',$lead) }}">{{ $lead->lead_no }}</a></td><td>{{ $lead->student_name ?? $lead->parent_name }}</td><td>{{ $lead->phone }}</td><td>{{ $lead->status?->name }}</td><td>{{ $lead->assignedUser?->name }}</td><td>{{ $lead->last_activity_at?->format('d M Y') ?? 'Never' }}</td></tr>@endforeach</tbody></table>{{ $leads->links() }}</div></div>
@endsection
