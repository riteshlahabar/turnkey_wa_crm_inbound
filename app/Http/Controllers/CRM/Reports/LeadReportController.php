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

class LeadReportController extends Controller
{
    public function index()
    {
        $leads = CrmLead::with(['status', 'source', 'priority', 'standard', 'course', 'assignedUser'])->latest()->paginate(30)->withQueryString();
        return view('crm.reports.lead-report.index', compact('leads'));
    }
}
