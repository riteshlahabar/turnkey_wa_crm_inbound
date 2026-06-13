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

class ReEngagementReportController extends Controller
{
    public function index()
    {
        $leads = CrmLead::with(['status', 'assignedUser'])->where(function ($q) { $q->whereNull('last_activity_at')->orWhere('last_activity_at', '<=', now()->subDays(7)); })->whereDoesntHave('status', fn ($q) => $q->where('is_final', true))->latest()->paginate(30);
        return view('crm.reports.re-engagement.index', compact('leads'));
    }
}
