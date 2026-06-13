@extends('layouts.app')
@section('title','Services')
@section('content')
@include('crm._page_header', ['title' => 'Add Services', 'subtitle' => 'Manage services master and fee template
amount'])
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Add Services</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.courses.store') }}">@csrf
                <div class="mb-2"><label class="form-label">Service Name</label><input name="name"
                            class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Level</label><select name="standard_id"
                            class="form-select">
                            <option value="">None</option>@foreach($standards as $standard)<option
                                value="{{ $standard->id }}">{{ $standard->name }}</option>@endforeach
                        </select></div>
                    
                    <div class="mb-2"><label class="form-label">Fee Amount</label><input type="number" step="0.01"
                            name="fee_amount" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Sort Order</label><input type="number" name="sort_order"
                            value="0" class="form-control"></div>
                    <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" checked
                            class="form-check-input"><label class="form-check-label">Active</label></div><button
                        class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Service List</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            
                            <th>Service Name</th>
                            <th>Level</th>
                            <th>Fee</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>@foreach($items as $item)<tr>
                            <form method="POST" action="{{ route('settings.courses.update', $item) }}">@csrf
                                @method('PUT')
                                <td><input name="name" class="form-control form-control-sm" value="{{ $item->name }}">
                                </td>
                                <td><select name="standard_id" class="form-select form-select-sm">
                                        <option value="">None</option>@foreach($standards as $standard)<option
                                            value="{{ $standard->id }}" @selected($item->standard_id==$standard->id)>{{
                                            $standard->name }}</option>@endforeach
                                    </select></td>
                                
                                <td><input type="number" step="0.01" name="fee_amount"
                                        class="form-control form-control-sm" value="{{ $item->fee_amount }}"></td>
                                <td><input type="number" name="sort_order" class="form-control form-control-sm"
                                        value="{{ $item->sort_order }}"></td>
                                <td><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active
                                </td>
                                <td><button class="btn btn-sm btn-primary">Update</button>
                            </form>
                            <form method="POST" action="{{ route('settings.courses.destroy', $item) }}" class="d-inline"
                                onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button
                                    class="btn btn-sm btn-light border text-danger">Delete</button></form>
                            </td>
                        </tr>@endforeach</tbody>
                </table>{{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection