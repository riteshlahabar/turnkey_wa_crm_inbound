@extends('layouts.app')
@section('title','Edit Lead')
@section('content')
@include('crm._page_header', ['title' => 'Edit Lead', 'subtitle' => $lead->lead_no])
<div class="card"><div class="card-body"><form method="POST" action="{{ route('leads.update', $lead) }}">@method('PUT') @include('crm.leads._form', ['button' => 'Update Lead'])</form></div></div>
@endsection
