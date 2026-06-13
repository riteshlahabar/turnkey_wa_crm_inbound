@extends('layouts.app')
@section('title','Admission Report')
@section('content')
@include('crm._page_header', ['title' => 'Admission Report', 'subtitle' => 'Leads converted to Admission Done'])
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Lead No</th>
                    <th>Student</th>
                    <th>Parent</th>
                    <th>Phone</th>
                    <th>Level</th>
                    <th>Service</th>
                    <th>Source</th>
                    <th>User</th>
                    <th>Admission Date</th>
                </tr>
            </thead>
            <tbody>@foreach($leads as $lead)<tr>
                    <td><a href="{{ route('leads.show',$lead) }}">{{ $lead->lead_no }}</a></td>
                    <td>{{ $lead->student_name }}</td>
                    <td>{{ $lead->parent_name }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ $lead->standard?->name }}</td>
                    <td>{{ $lead->course?->name }}</td>
                    <td>{{ $lead->source?->name }}</td>
                    <td>{{ $lead->assignedUser?->name }}</td>
                    <td>{{ $lead->admission_done_at?->format('d M Y') }}</td>
                </tr>@endforeach</tbody>
        </table>{{ $leads->links() }}
    </div>
</div>
@endsection