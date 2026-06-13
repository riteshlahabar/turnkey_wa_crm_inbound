@extends('layouts.app')

@section('title','Add Lead')

@section('content')
@include('crm._page_header', [
    'title' => 'Add Lead',
    'subtitle' => 'Manual / walk-in tuition inquiry'
])

<div class="card">
    <div class="card-body">
        <form method="POST"
              action="{{ route('leads.store') }}"
              id="leadForm"
              class="lead-form"
              novalidate>
            @include('crm.leads._form', ['button' => 'Create Lead'])
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('leadForm');

    if (!form) {
        return;
    }

    const rules = {
        parent_name: {
            required: true,
            label: 'Parent name',
            min: 2,
            max: 80,
            regex: /^[A-Za-z\s.'-]+$/,
            message: 'Parent name can contain only letters, spaces, dot, apostrophe and hyphen.'
        },
        student_name: {
            required: true,
            label: 'Student name',
            min: 2,
            max: 80,
            regex: /^[A-Za-z\s.'-]+$/,
            message: 'Student name can contain only letters, spaces, dot, apostrophe and hyphen.'
        },
        phone: {
            required: true,
            label: 'Mobile number',
            regex: /^[6-9][0-9]{9}$/,
            message: 'Enter valid 10 digit mobile number starting with 6, 7, 8 or 9.'
        },
        alternate_phone: {
            required: false,
            label: 'Alternate number',
            regex: /^[6-9][0-9]{9}$/,
            message: 'Enter valid 10 digit alternate number starting with 6, 7, 8 or 9.'
        },
        standard_id: {
            required: true,
            label: 'Level'
        },
        course_id: {
            required: true,
            label: 'Service'
        },
        school_name: {
            required: false,
            label: 'School name',
            max: 120,
            regex: /^[A-Za-z0-9\s.,&'()-]+$/,
            message: 'School name contains invalid characters.'
        },
        board: {
            required: false,
            label: 'Board',
            max: 50,
            regex: /^[A-Za-z0-9\s/-]+$/,
            message: 'Board contains invalid characters.'
        },
        inquiry_date: {
            required: true,
            label: 'Inquiry date',
            dateNotFuture: true
        },
        lead_source_id: {
            required: true,
            label: 'Lead source'
        },
        lead_status_id: {
            required: true,
            label: 'Lead status'
        },
        lead_priority_id: {
            required: true,
            label: 'Lead priority'
        },
        assigned_user_id: {
            required: true,
            label: 'Assigned user'
        },
        next_followup_date: {
            required: false,
            label: 'Next follow-up date'
        },
        next_followup_time: {
            required: false,
            label: 'Next follow-up time'
        },
        address: {
            required: false,
            label: 'Address / Area',
            max: 500
        },
        note: {
            required: false,
            label: 'Note',
            max: 1000
        }
    };

    function getField(name) {
        return form.querySelector('[name="' + name + '"]');
    }

    function getFieldBox(field) {
        return field.closest('.field-box') || field.parentElement;
    }

    function removeError(field) {
        field.classList.remove('is-invalid');

        const box = getFieldBox(field);
        const oldError = box.querySelector('.front-error');

        if (oldError) {
            oldError.remove();
        }
    }

    function showError(field, message) {
        removeError(field);

        field.classList.add('is-invalid');

        const box = getFieldBox(field);
        const error = document.createElement('div');

        error.className = 'invalid-feedback front-error';
        error.innerText = message;

        box.appendChild(error);
    }

    function isEmpty(value) {
        return value === null || value.trim() === '';
    }

    function todayDateOnly() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return today;
    }

    function parseDate(value) {
        const date = new Date(value + 'T00:00:00');
        date.setHours(0, 0, 0, 0);
        return date;
    }

    function validateField(name) {
        const field = getField(name);
        const rule = rules[name];

        if (!field || !rule) {
            return true;
        }

        removeError(field);

        const value = field.value.trim();

        if (rule.required && isEmpty(value)) {
            showError(field, rule.label + ' is required.');
            return false;
        }

        if (!rule.required && isEmpty(value)) {
            return true;
        }

        if (rule.min && value.length < rule.min) {
            showError(field, rule.label + ' must be at least ' + rule.min + ' characters.');
            return false;
        }

        if (rule.max && value.length > rule.max) {
            showError(field, rule.label + ' must not be greater than ' + rule.max + ' characters.');
            return false;
        }

        if (rule.regex && !rule.regex.test(value)) {
            showError(field, rule.message || (rule.label + ' is invalid.'));
            return false;
        }

        if (rule.dateNotFuture) {
            const selectedDate = parseDate(value);

            if (selectedDate > todayDateOnly()) {
                showError(field, rule.label + ' cannot be future date.');
                return false;
            }
        }

        return true;
    }

    function validateNextFollowup() {
        const nextDate = getField('next_followup_date');
        const nextTime = getField('next_followup_time');

        if (!nextDate || !nextTime) {
            return true;
        }

        let valid = true;

        removeError(nextDate);
        removeError(nextTime);

        const dateValue = nextDate.value.trim();
        const timeValue = nextTime.value.trim();

        if (dateValue !== '' && timeValue === '') {
            showError(nextTime, 'Next follow-up time is required when date is selected.');
            valid = false;
        }

        if (timeValue !== '' && dateValue === '') {
            showError(nextDate, 'Next follow-up date is required when time is selected.');
            valid = false;
        }

        if (dateValue !== '' && timeValue !== '') {
            const selectedDateTime = new Date(dateValue + 'T' + timeValue + ':00');
            const now = new Date();

            if (selectedDateTime < now) {
                showError(nextDate, 'Next follow-up date/time cannot be in the past.');
                valid = false;
            }
        }

        return valid;
    }

    function sanitizeInputs() {
        const onlyDigits = ['phone', 'alternate_phone'];

        onlyDigits.forEach(function (name) {
            const field = getField(name);

            if (field) {
                field.value = field.value.replace(/[^0-9]/g, '').slice(0, 10);
            }
        });

        const nameFields = ['parent_name', 'student_name'];

        nameFields.forEach(function (name) {
            const field = getField(name);

            if (field) {
                field.value = field.value.replace(/[^A-Za-z\s.'-]/g, '').slice(0, 80);
            }
        });

        const school = getField('school_name');

        if (school) {
            school.value = school.value.replace(/[^A-Za-z0-9\s.,&'()-]/g, '').slice(0, 120);
        }

        const board = getField('board');

        if (board) {
            board.value = board.value.replace(/[^A-Za-z0-9\s/-]/g, '').slice(0, 50);
        }
    }

    form.addEventListener('input', function (event) {
        sanitizeInputs();

        if (event.target.name && rules[event.target.name]) {
            removeError(event.target);
        }
    });

    form.addEventListener('change', function (event) {
        if (event.target.name && rules[event.target.name]) {
            removeError(event.target);
        }
    });

    form.addEventListener('submit', function (event) {
        sanitizeInputs();

        let isValid = true;

        Object.keys(rules).forEach(function (name) {
            if (!validateField(name)) {
                isValid = false;
            }
        });

        if (!validateNextFollowup()) {
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();

            const firstInvalid = form.querySelector('.is-invalid');

            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                setTimeout(function () {
                    firstInvalid.focus();
                }, 300);
            }
        }
    });
});
</script>
@endsection