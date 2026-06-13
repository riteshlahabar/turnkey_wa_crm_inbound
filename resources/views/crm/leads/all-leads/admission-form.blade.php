<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admission Form {{ $lead->lead_no }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="text-center mb-4">
            <h3>Student Admission / Enrollment Form</h3>
            <p>Lead No: {{ $lead->lead_no }}</p>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Student Name</th>
                <td>{{ $lead->student_name }}</td>
                <th>Parent Name</th>
                <td>{{ $lead->parent_name }}</td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>{{ $lead->phone }}</td>
                <th>Alt Mobile</th>
                <td>{{ $lead->alternate_phone }}</td>
            </tr>
            <tr>
                <th>Level</th>
                <td>{{ $lead->standard?->name }}</td>
                <th>Service</th>
                <td>{{ $lead->course?->name }}</td>
            </tr>
            <tr>
                <th>School</th>
                <td>{{ $lead->school_name }}</td>
                <th>Board</th>
                <td>{{ $lead->board }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td colspan="3">{{ $lead->address }}</td>
            </tr>
            <tr>
                <th>Admission Date</th>
                <td>{{ $lead->admission_done_at?->format('d M Y') ?? now()->format('d M Y') }}</td>
                <th>Counsellor</th>
                <td>{{ $lead->assignedUser?->name }}</td>
            </tr>
        </table>
        <div class="row mt-5">
            <div class="col-6">Parent Signature: __________________</div>
            <div class="col-6 text-end">Office Signature: __________________</div>
        </div><button onclick="window.print()" class="btn btn-primary no-print mt-4">Print Admission Form</button>
    </div>
</body>

</html>