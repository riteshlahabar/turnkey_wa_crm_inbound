@extends('layouts.app')
@section('title','Follow-up Report')
@section('content')
@include('crm._page_header', ['title' => 'Follow-up Report', 'subtitle' => 'User-wise follow-up work'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>User</th><th>Total</th><th>Completed</th><th>Pending</th><th>Overdue</th></tr></thead><tbody>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->total_followups_count }}</td><td>{{ $user->completed_followups_count }}</td><td>{{ $user->pending_followups_count }}</td><td>{{ $user->overdue_followups_count }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
