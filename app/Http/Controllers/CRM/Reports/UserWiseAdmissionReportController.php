<?php

namespace App\Http\Controllers\CRM\Reports;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmFeeQuotation;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmLeadSource;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmStandard;
use App\Models\User;

class UserWiseAdmissionReportController extends Controller
{
    public function index()
    {
        $admissionStatusIds = CrmLeadStatus::where('is_admission', true)->pluck('id');
        $users = User::withCount([
            'leads as total_leads_count',
            'leads as admission_count' => fn ($q) => $q->whereIn('lead_status_id', $admissionStatusIds),
        ])->orderByDesc('admission_count')->get();
        return view('crm.reports.user-wise-admission.index', compact('users'));
    }
}
