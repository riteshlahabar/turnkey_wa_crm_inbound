@extends('layouts.app')
@section('title','Source Wise Report')
@section('content')
@include('crm._page_header', ['title' => 'Source Wise Report', 'subtitle' => 'Lead source performance'])
<div class="card"><div class="card-body table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Source</th><th>Total Leads</th><th>Admissions</th><th>Conversion %</th></tr></thead><tbody>@foreach($sources as $source)@php($conv = $source->total_leads_count ? round(($source->admission_count/$source->total_leads_count)*100,2) : 0)<tr><td>{{ $source->name }}</td><td>{{ $source->total_leads_count }}</td><td>{{ $source->admission_count }}</td><td>{{ $conv }}%</td></tr>@endforeach</tbody></table></div></div>
@endsection
