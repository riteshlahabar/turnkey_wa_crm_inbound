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

class LostLeadReportController extends Controller
{
    public function index()
    {
        $lostStatusIds = CrmLeadStatus::whereIn('name', ['Lost', 'Not Interested'])->pluck('id');
        $leads = CrmLead::with(['status', 'source', 'assignedUser'])->whereIn('lead_status_id', $lostStatusIds)->latest()->paginate(30); 
        return view('crm.reports.lost-lead.index', compact('leads'));
    }
}
