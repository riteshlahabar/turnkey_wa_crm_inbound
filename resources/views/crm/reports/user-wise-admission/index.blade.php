@extends('layouts.app')
@section('title','User Wise Admission Report')
@section('content')
@include('crm._page_header', ['title' => 'User Wise Admission Report', 'subtitle' => 'Which user gave more admissions'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>User</th><th>Total Leads</th><th>Admissions</th><th>Conversion %</th><th>Target</th></tr></thead><tbody>@foreach($users as $user)@php($conv = $user->total_leads_count ? round(($user->admission_count/$user->total_leads_count)*100,2) : 0)<tr><td>{{ $user->name }}</td><td>{{ $user->total_leads_count }}</td><td>{{ $user->admission_count }}</td><td>{{ $conv }}%</td><td>{{ $user->monthly_target ?? 0 }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
