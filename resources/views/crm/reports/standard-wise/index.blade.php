@extends('layouts.app')
@section('title','Level Wise Report')
@section('content')
@include('crm._page_header', ['title' => 'Level Wise Report', 'subtitle' => 'Level-wise inquiries and
admissions'])
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Total Leads</th>
                    <th>Admissions</th>
                    <th>Conversion %</th>
                </tr>
            </thead>
            <tbody>@foreach($standards as $item)@php($conv = $item->total_leads_count ?
                round(($item->admission_count/$item->total_leads_count)*100,2) : 0)<tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->total_leads_count }}</td>
                    <td>{{ $item->admission_count }}</td>
                    <td>{{ $conv }}%</td>
                </tr>@endforeach</tbody>
        </table>
    </div>
</div>
@endsection