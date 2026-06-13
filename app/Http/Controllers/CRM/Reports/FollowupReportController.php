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

class FollowupReportController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'createdFollowups as total_followups_count',
            'createdFollowups as completed_followups_count' => fn ($q) => $q->where('status', 'completed'),
            'createdFollowups as pending_followups_count' => fn ($q) => $q->where('status', 'pending'),
            'createdFollowups as overdue_followups_count' => fn ($q) => $q->where('status', 'pending')->where('followup_at', '<', now()),
        ])->orderByDesc('total_followups_count')->get();
        return view('crm.reports.followup.index', compact('users'));
    }
}
