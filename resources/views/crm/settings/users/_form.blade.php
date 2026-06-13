<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', optional($user)->name) }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email', optional($user)->email) }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Mobile</label>
        <input type="text" name="mobile" value="{{ old('mobile', optional($user)->mobile) }}" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Profile Image</label>
        <input type="file" name="profile_image" class="form-control" accept="image/*">
        @if(optional($user)->profile_image)
            <img src="{{ asset($user->profile_image) }}" class="rounded-circle mt-2" style="width:52px;height:52px;object-fit:cover;" alt="profile">
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Password @if($passwordRequired)<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password" class="form-control" @if($passwordRequired) required @endif placeholder="{{ $passwordRequired ? '' : 'Leave blank to keep old password' }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
           @foreach(['counsellor','receptionist','teacher','staff'] as $role)
                <option value="{{ $role }}" @selected(old('role', optional($user)->role ?? 'staff') == $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', optional($user)->status ?? 'active') == 'active')>Active</option>
            <option value="inactive" @selected(old('status', optional($user)->status) == 'inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Monthly Admission Target</label>
        <input type="number" name="monthly_target" value="{{ old('monthly_target', optional($user)->monthly_target ?? 0) }}" class="form-control" min="0">
    </div>
</div>
