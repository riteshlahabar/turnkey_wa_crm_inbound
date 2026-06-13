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

class AdmissionReportController extends Controller
{
    public function index()
    {
        $admissionStatusIds = CrmLeadStatus::where('is_admission', true)->pluck('id');
        $leads = CrmLead::with(['status', 'source', 'standard', 'course', 'assignedUser'])->whereIn('lead_status_id', $admissionStatusIds)->latest('admission_done_at')->paginate(30)->withQueryString();
        return view('crm.reports.admission.index', compact('leads'));
    }
}
