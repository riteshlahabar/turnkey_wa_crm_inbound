<?php

namespace App\Http\Controllers\CRM\Followups;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\Concerns\CrmFormOptions;
use App\Models\CRM\CrmActivity;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmLead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmClosedLeadStatus;

class FollowupController extends Controller
{
    use CrmFormOptions;

    public function index(Request $request)
{
    $tab = $request->get('tab', 'today');

    $baseQuery = CrmFollowup::query()
        ->whereHas('lead', function ($q) {
            $q->where(function ($sub) {
                $sub->where('is_closed', false)
                    ->orWhereNull('is_closed');
            });
        });

    if ($tab === 'today') {
        $baseQuery->whereDate('followup_at', today())
            ->where('status', 'pending');
    }

    if ($tab === 'pending') {
        // Important: no date filter here
        $baseQuery->where('status', 'pending');
    }

    if ($tab === 'overdue') {
        $baseQuery->where('status', 'pending')
            ->where('followup_at', '<', now());
    }

    if ($tab === 'completed') {
        $baseQuery->where('status', 'completed');
    }

    if ($request->filled('type_id')) {
        $baseQuery->where('followup_type_id', $request->type_id);
    }

    if ($request->filled('user_id')) {
        $baseQuery->whereHas('lead', function ($q) use ($request) {
            $q->where('assigned_user_id', $request->user_id);
        });
    }

    if ($request->filled('standard_id')) {
        $baseQuery->whereHas('lead', function ($q) use ($request) {
            $q->where('standard_id', $request->standard_id);
        });
    }

    if ($request->filled('course_id')) {
        $baseQuery->whereHas('lead', function ($q) use ($request) {
            $q->where('course_id', $request->course_id);
        });
    }

    if ($request->filled('search')) {
        $search = $request->search;

        $baseQuery->whereHas('lead', function ($q) use ($search) {
            $q->where('lead_no', 'like', "%{$search}%")
                ->orWhere('parent_name', 'like', "%{$search}%")
                ->orWhere('student_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhereHas('standard', function ($standardQuery) use ($search) {
                    $standardQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function ($courseQuery) use ($search) {
                    $courseQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    $authUser = auth()->user();

    if ($authUser && ($authUser->role ?? 'admin') !== 'admin') {
        $baseQuery->whereHas('lead', function ($q) use ($authUser) {
            $q->where('assigned_user_id', $authUser->id);
        });
    }

    if ($tab === 'all') {
        // All tab = latest one follow-up per lead
        $latestIds = (clone $baseQuery)
            ->orderByDesc('followup_at')
            ->orderByDesc('id')
            ->get()
            ->unique('lead_id')
            ->pluck('id')
            ->values();
    } else {
        // Today/Pending/Overdue/Completed = show all matching records
        $latestIds = (clone $baseQuery)
            ->orderByDesc('followup_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->values();
    }

    $followups = CrmFollowup::query()
        ->with([
            'lead.standard',
            'lead.course',
            'lead.status',
            'lead.assignedUser',
            'lead.quotations.course',
            'lead.followups' => function ($query) {
                $query->with(['type', 'creator', 'assignedUser'])
                    ->orderByDesc('followup_at')
                    ->orderByDesc('id');
            },
            'type',
            'creator',
            'assignedUser',
        ])
        ->select('crm_followups.*')
        ->selectSub(function ($query) {
            $query->from('crm_followups as followup_count_table')
                ->selectRaw('COUNT(*)')
                ->whereColumn('followup_count_table.lead_id', 'crm_followups.lead_id');
        }, 'followup_times')
        ->whereIn('id', $latestIds)
        ->orderByDesc('followup_at')
        ->orderByDesc('id')
        ->paginate(20)
        ->withQueryString();

    $closedStatuses = \App\Models\CRM\CrmClosedLeadStatus::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('crm.followups.index', array_merge(
        $this->formOptions(),
        compact('followups', 'tab', 'closedStatuses')
    ));
}

public function store(Request $request, CrmLead $lead)
{
    $data = $request->validate([
        'followup_type_id' => 'nullable|exists:crm_followup_types,id',
        'followup_date' => 'required|date',
        'followup_time' => 'required|date_format:H:i',
        'note' => 'nullable|string',
    ]);

    $openFollowupExists = CrmFollowup::where('lead_id', $lead->id)
        ->where('status', 'pending')
        ->exists();

    if ($openFollowupExists) {
        return back()->with('error', 'Please complete the current follow-up before creating a new follow-up.');
    }

    $followupAt = Carbon::parse($data['followup_date'] . ' ' . $data['followup_time']);

    CrmFollowup::create([
        'lead_id' => $lead->id,
        'followup_type_id' => $data['followup_type_id'] ?? null,
        'assigned_user_id' => $lead->assigned_user_id,
        'status' => 'pending',
        'followup_at' => $followupAt,
        'next_followup_at' => $followupAt,
        'note' => $data['note'] ?? null,
        'created_by' => auth()->id(),
    ]);

    $lead->update([
        'next_followup_at' => $followupAt,
        'last_activity_at' => now(),
    ]);

    CrmActivity::create([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'type' => 'followup_added',
        'description' => 'Follow-up added for ' . $followupAt->format('d M Y h:i A'),
    ]);

    return back()->with('success', 'Follow-up saved successfully.');
}

public function complete(Request $request, CrmFollowup $followup)
{
    if ($followup->status === 'completed') {
        return redirect()
            ->route('followups.index', ['tab' => 'completed'])
            ->with('error', 'This follow-up is already completed.');
    }

    $followup->load('lead');

    $followup->forceFill([
        'status' => 'completed',
        'completed_at' => now(),
        'updated_at' => now(),
    ])->save();

    if ($followup->lead) {
        $followup->lead->update([
            'last_activity_at' => now(),
        ]);
    }

    CrmActivity::create([
        'lead_id' => $followup->lead_id,
        'user_id' => auth()->id(),
        'type' => 'followup_completed',
        'description' => 'Follow-up marked completed.',
    ]);

    return redirect()
        ->route('followups.index', ['tab' => 'completed'])
        ->with('success', 'Follow-up completed successfully. Create next follow-up from here.');
}

public function closeLead(Request $request, CrmFollowup $followup)
{
    $data = $request->validate([
        'closed_status_id' => 'required|exists:crm_closed_lead_statuses,id',
        'note' => 'nullable|string',
    ]);

    $followup->load('lead');

    $closedStatus = CrmClosedLeadStatus::find($data['closed_status_id']);

    $followup->lead->forceFill([
        'closed_status_id' => $data['closed_status_id'],
        'is_closed' => true,
        'closed_at' => now(),
        'closed_by' => $followup->lead?->assigned_user_id ?? auth()->id(),
        'closed_note' => $data['note'] ?? null,
        'next_followup_at' => $followupAt,
        'last_activity_at' => now(),
    ])->save();

    CrmFollowup::where('lead_id', $followup->lead_id)
        ->where('status', 'pending')
        ->update([
            'status' => 'completed',
            'completed_at' => now(),
            'note' => $data['note'] ?? 'Lead closed.',
            'updated_at' => now(),
        ]);

    CrmActivity::create([
        'lead_id' => $followup->lead_id,
        'user_id' => auth()->id(),
        'type' => 'lead_closed',
        'description' => 'Lead closed with status: ' . ($closedStatus?->name ?? 'N/A'),
    ]);

    return back()->with('success', 'Lead closed successfully.');
}

public function changeType(Request $request, CrmFollowup $followup)
{
    $data = $request->validate([
        'followup_type_id' => 'required|exists:crm_followup_types,id',
    ]);

    $followup->update([
        'followup_type_id' => $data['followup_type_id'],
        'updated_at' => now(),
    ]);

    CrmActivity::create([
        'lead_id' => $followup->lead_id,
        'user_id' => auth()->id(),
        'type' => 'followup_type_changed',
        'description' => 'Follow-up type changed.',
    ]);

    return back()->with('success', 'Follow-up type changed successfully.');
}

public function createNextFollowup(Request $request, CrmFollowup $followup)
{
    $data = $request->validate([
        'followup_type_id' => 'nullable|exists:crm_followup_types,id',
        'next_followup_date' => 'required|date',
        'next_followup_time' => 'required|date_format:H:i',
        'note' => 'nullable|string',
    ]);

    if ($followup->status !== 'completed') {
        return back()->with('error', 'Please complete current follow-up before creating next follow-up.');
    }

    $followup->load('lead');

    $openFollowupExists = CrmFollowup::where('lead_id', $followup->lead_id)
        ->where('status', 'pending')
        ->exists();

    if ($openFollowupExists) {
        return back()->with('error', 'One pending follow-up already exists for this lead.');
    }

    $nextAt = Carbon::parse($data['next_followup_date'] . ' ' . $data['next_followup_time']);

    CrmFollowup::create([
        'lead_id' => $followup->lead_id,
        'followup_type_id' => $data['followup_type_id'] ?? $followup->followup_type_id,
        'assigned_user_id' => $followup->lead?->assigned_user_id ?? $followup->assigned_user_id,
        'status' => 'pending',
        'followup_at' => $nextAt,
        'next_followup_at' => $nextAt,
        'note' => $data['note'] ?? 'Next follow-up',
        'created_by' => auth()->id(),
    ]);

    if ($followup->lead) {
        $followup->lead->update([
            'next_followup_at' => $nextAt,
            'last_activity_at' => now(),
        ]);
    }

    CrmActivity::create([
        'lead_id' => $followup->lead_id,
        'user_id' => auth()->id(),
        'type' => 'next_followup_created',
        'description' => 'Next follow-up created for ' . $nextAt->format('d M Y h:i A'),
    ]);

    $redirectTab = $nextAt->isToday() ? 'today' : 'pending';

    return redirect()
        ->route('followups.index', ['tab' => $redirectTab])
        ->with('success', 'Next follow-up created successfully.');
}

    public function destroy(CrmFollowup $followup)
    {
        $followup->delete();
        return back()->with('success', 'Follow-up deleted successfully.');
    }
}
