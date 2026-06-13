@extends('layouts.app')

@section('title','Counsellor Performance')

@section('content')
@include('crm._page_header', [
    'title' => 'Counsellor Performance Report',
    'subtitle' => 'Leads, follow-ups, admissions and target'
])

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Total Leads</th>
                    <th>Follow-ups</th>
                    <th>Admissions</th>
                    <th>Target</th>
                    <th>Target %</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                    @php
                        $target = $user->monthly_target ?: 0;
                        $targetPercent = $target ? round(($user->admission_count / $target) * 100, 2) : 0;
                    @endphp

                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->total_leads_count }}</td>
                        <td>{{ $user->followups_count }}</td>
                        <td>{{ $user->admission_count }}</td>
                        <td>{{ $target }}</td>
                        <td>{{ $targetPercent }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection