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

class PendingFollowupReportController extends Controller
{
    public function index()
    {
        $followups = CrmFollowup::with(['lead.assignedUser', 'type'])->where('status', 'pending')->orderBy('followup_at')->paginate(30); 
        return view('crm.reports.pending-followup.index', compact('followups'));
    }
}
