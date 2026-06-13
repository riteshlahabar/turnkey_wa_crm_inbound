@csrf

<div class="row g-3">
    <div class="col-md-4 field-box">
        <label class="form-label">
            Parent Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="parent_name"
               class="form-control"
               value="{{ old('parent_name', $lead->parent_name ?? '') }}"
               required
               maxlength="80"
               autocomplete="off"
               placeholder="Enter parent name">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Student Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="student_name"
               class="form-control"
               value="{{ old('student_name', $lead->student_name ?? '') }}"
               required
               maxlength="80"
               autocomplete="off"
               placeholder="Enter student name">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Mobile Number <span class="text-danger">*</span>
        </label>
        <input type="tel"
               name="phone"
               class="form-control"
               value="{{ old('phone', $lead->phone ?? '') }}"
               required
               minlength="10"
               maxlength="10"
               inputmode="numeric"
               pattern="[6-9][0-9]{9}"
               autocomplete="off"
               placeholder="10 digit mobile number">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">Alternate Number</label>
        <input type="tel"
               name="alternate_phone"
               class="form-control"
               value="{{ old('alternate_phone', $lead->alternate_mobile ?? '') }}"
               minlength="10"
               maxlength="10"
               inputmode="numeric"
               pattern="[6-9][0-9]{9}"
               autocomplete="off"
               placeholder="10 digit alternate number">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Level <span class="text-danger">*</span>
        </label>
        <select name="standard_id"
                class="form-select"
                required>
            <option value="">Select Level</option>
            @foreach($standards as $standard)
                <option value="{{ $standard->id }}" @selected(old('standard_id', $lead->standard_id ?? '') == $standard->id)>
                    {{ $standard->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Service <span class="text-danger">*</span>
        </label>
        <select name="course_id"
                class="form-select"
                required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected(old('course_id', $lead->course_id ?? '') == $course->id)>
                    {{ $course->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">School Name</label>
        <input type="text"
               name="school_name"
               class="form-control"
               value="{{ old('school_name', $lead->school_name ?? '') }}"
               maxlength="120"
               autocomplete="off"
               placeholder="Enter school name">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">Board</label>
        <input type="text"
               name="board"
               class="form-control"
               value="{{ old('board', $lead->board ?? '') }}"
               maxlength="50"
               autocomplete="off"
               placeholder="CBSE / SSC / ICSE">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Inquiry Date <span class="text-danger">*</span>
        </label>
        <input type="date"
               name="inquiry_date"
               class="form-control"
               value="{{ old('inquiry_date', isset($lead) && $lead->inquiry_date ? $lead->inquiry_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required
               max="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Lead Source <span class="text-danger">*</span>
        </label>
        <select name="lead_source_id"
                class="form-select"
                required>
            <option value="">Select Lead Source</option>
            @foreach($sources as $source)
                <option value="{{ $source->id }}" @selected(old('lead_source_id', $lead->lead_source_id ?? '') == $source->id)>
                    {{ $source->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Lead Status <span class="text-danger">*</span>
        </label>
        <select name="lead_status_id"
                class="form-select"
                required>
            <option value="">Select Lead Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" @selected(old('lead_status_id', $lead->lead_status_id ?? '') == $status->id)>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Lead Priority <span class="text-danger">*</span>
        </label>
        <select name="lead_priority_id"
                class="form-select"
                required>
            <option value="">Select Lead Priority</option>
            @foreach($priorities as $priority)
                <option value="{{ $priority->id }}" @selected(old('lead_priority_id', $lead->lead_priority_id ?? '') == $priority->id)>
                    {{ $priority->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">
            Assigned User <span class="text-danger">*</span>
        </label>
        <select name="assigned_user_id"
                class="form-select"
                required>
            <option value="">Select Assigned User</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_user_id', $lead->assigned_user_id ?? '') == $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">Next Follow-up Date</label>
        <input type="date"
               name="next_followup_date"
               class="form-control"
               value="{{ old('next_followup_date', isset($lead) && $lead->next_followup_at ? $lead->next_followup_at->format('Y-m-d') : '') }}"
               min="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="col-md-4 field-box">
        <label class="form-label">Next Follow-up Time</label>
        <input type="time"
               name="next_followup_time"
               class="form-control"
               value="{{ old('next_followup_time', isset($lead) && $lead->next_followup_at ? $lead->next_followup_at->format('H:i') : '') }}">
    </div>

    <div class="col-md-12 field-box">
        <label class="form-label">Address / Area</label>
        <textarea name="address"
                  class="form-control"
                  rows="2"
                  maxlength="500"
                  placeholder="Enter address or area">{{ old('address', $lead->address ?? '') }}</textarea>
    </div>

    <div class="col-md-12 field-box">
        <label class="form-label">Note</label>
        <textarea name="note"
                  class="form-control"
                  rows="3"
                  maxlength="1000"
                  placeholder="Enter note">{{ old('note', $lead->note ?? '') }}</textarea>
    </div>
</div>

<div class="d-flex align-items-center gap-2 mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="iconoir-check-circle me-1"></i>{{ $button ?? 'Save Lead' }}
    </button>

    <a href="{{ route('leads.index') }}" class="btn btn-light border">
        Cancel
    </a>
</div>