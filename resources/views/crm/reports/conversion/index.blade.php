@extends('layouts.app')
@section('title','Conversion Report')
@section('content')
@include('crm._page_header', ['title' => 'Conversion Report', 'subtitle' => 'Overall admission conversion percentage'])
<div class="row"><div class="col-md-4"><div class="card"><div class="card-body text-center"><p>Total Leads</p><h2>{{ $totalLeads }}</h2></div></div></div><div class="col-md-4"><div class="card"><div class="card-body text-center"><p>Admissions</p><h2>{{ $admissions }}</h2></div></div></div><div class="col-md-4"><div class="card"><div class="card-body text-center"><p>Conversion</p><h2>{{ $conversion }}%</h2></div></div></div></div>
@endsection
