@extends('layouts.app')
@section('title', $title)
@section('content')
@include('crm._page_header', ['title' => $title, 'subtitle' => 'Manage dynamic CRM master data'])
<div class="row">
    <div class="col-lg-4">
        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Add {{ $title }}</h4></div><div class="card-body">
            <form method="POST" action="{{ route($routePrefix.'.store') }}">@csrf
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                @foreach($extraFields as $field => $config)
                    <div class="mb-3"><label class="form-label">{{ ucwords(str_replace('_',' ', $field)) }}</label><input type="text" name="{{ $field }}" class="form-control" value="{{ $field == 'color' ? 'primary' : '' }}"></div>
                @endforeach
                <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Active</label></div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header"><h4 class="card-title mb-0">{{ $title }} List</h4></div><div class="card-body table-responsive">
            <table class="table table-hover align-middle"><thead class="table-light"><tr><th>Name</th>@foreach($extraFields as $field => $config)<th>{{ ucwords(str_replace('_',' ', $field)) }}</th>@endforeach<th>Order</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @foreach($items as $item)
                <tr><form method="POST" action="{{ route($routePrefix.'.update', $item->id) }}">@csrf @method('PUT')
                    <td><input name="name" value="{{ $item->name }}" class="form-control form-control-sm"></td>
                    @foreach($extraFields as $field => $config)<td><input name="{{ $field }}" value="{{ $item->{$field} }}" class="form-control form-control-sm"></td>@endforeach
                    <td><input type="number" name="sort_order" value="{{ $item->sort_order }}" class="form-control form-control-sm" style="width:90px"></td>
                    <td><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</td>
                    <td><button class="btn btn-sm btn-primary">Update</button></form><form method="POST" action="{{ route($routePrefix.'.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light border text-danger">Delete</button></form></td>
                </tr>
                @endforeach
            </tbody></table>{{ $items->links() }}</div></div>
    </div>
</div>
@endsection
