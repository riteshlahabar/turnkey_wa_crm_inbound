<?php

namespace App\Http\Controllers\CRM\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCall;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmWhatsappLog;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $authUser = auth()->user();

    $leadQuery = CrmLead::query();

    if ($authUser && ($authUser->role ?? 'admin') !== 'admin') {
        $leadQuery->where('assigned_user_id', $authUser->id);
    }

    $activeLeadQuery = (clone $leadQuery)
        ->where(function ($q) {
            $q->where('is_closed', false)
                ->orWhereNull('is_closed');
        });

    $closedLeadQuery = (clone $leadQuery)
        ->where('is_closed', true);

    $followupQuery = CrmFollowup::query()
        ->whereHas('lead', function ($q) use ($authUser) {
            $q->where(function ($sub) {
                $sub->where('is_closed', false)
                    ->orWhereNull('is_closed');
            });

            if ($authUser && ($authUser->role ?? 'admin') !== 'admin') {
                $q->where('assigned_user_id', $authUser->id);
            }
        });

    $cards = [
        'total_leads' => (clone $activeLeadQuery)->count(),
        'total_followups' => (clone $followupQuery)->count(),
        'pending_followups' => (clone $followupQuery)->where('status', 'pending')->count(),
        'closed_leads' => (clone $closedLeadQuery)->count(),
    ];

    $userWiseReports = User::query()
        ->where('role', '!=', 'admin')
        ->withCount([
            'assignedLeads as total_leads_count' => function ($q) {
                $q->where(function ($sub) {
                    $sub->where('is_closed', false)
                        ->orWhereNull('is_closed');
                });
            },

            'assignedLeads as today_leads_count' => function ($q) {
                $q->whereDate('created_at', today())
                    ->where(function ($sub) {
                        $sub->where('is_closed', false)
                            ->orWhereNull('is_closed');
                    });
            },

            'assignedLeads as closed_leads_count' => function ($q) {
                $q->where('is_closed', true);
            },
        ])
        ->get()
        ->map(function ($user) {
            $followupBase = CrmFollowup::whereHas('lead', function ($q) use ($user) {
                $q->where('assigned_user_id', $user->id)
                    ->where(function ($sub) {
                        $sub->where('is_closed', false)
                            ->orWhereNull('is_closed');
                    });
            });

            $user->total_followups_count = (clone $followupBase)->count();

            $user->today_followups_count = (clone $followupBase)
                ->whereDate('followup_at', today())
                ->count();

            $user->pending_followups_count = (clone $followupBase)
                ->where('status', 'pending')
                ->count();

            $user->overdue_followups_count = (clone $followupBase)
                ->where('status', 'pending')
                ->where('followup_at', '<', now())
                ->count();

            $user->next_followup_at = (clone $followupBase)
                ->where('status', 'pending')
                ->where('followup_at', '>=', now())
                ->orderBy('followup_at')
                ->value('followup_at');

            return $user;
        });

    return view('crm.dashboard.index', compact('cards', 'userWiseReports'));
}
}
