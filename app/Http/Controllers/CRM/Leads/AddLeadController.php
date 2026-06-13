<?php

namespace App\Http\Controllers\CRM\Leads;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\Concerns\CrmFormOptions;
use App\Models\CRM\CrmActivity;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmLeadStatus;
use Illuminate\Http\Request;

class AddLeadController extends Controller
{
    use CrmFormOptions;

    public function create()
    {
        return view('crm.leads.add-lead.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_name' => 'nullable|string|max:255',
            'student_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'standard_id' => 'nullable|exists:crm_standards,id',
            'course_id' => 'nullable|exists:crm_courses,id',
            'school_name' => 'nullable|string|max:255',
            'board' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'lead_source_id' => 'nullable|exists:crm_lead_sources,id',
            'lead_status_id' => 'nullable|exists:crm_lead_statuses,id',
            'lead_priority_id' => 'nullable|exists:crm_lead_priorities,id',
            'assigned_user_id' => [
    'required',
    Rule::exists('users', 'id')->where(function ($query) {
        $query->where('role', '!=', 'admin')
              ->where('status', 'active');
    }),
],
            'inquiry_date' => 'nullable|date',
            'next_followup_date' => 'nullable|date',
            'next_followup_time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string',
        ]);

        $defaultStatus = CrmLeadStatus::where('is_default', true)->first();
        $nextFollowupAt = null;
        if (!empty($data['next_followup_date'])) {
            $nextFollowupAt = $data['next_followup_date'] . ' ' . ($data['next_followup_time'] ?? '10:00');
        }

        $lead = CrmLead::create([
            ...$data,
            'lead_no' => $this->newLeadNumber(),
            'lead_status_id' => $data['lead_status_id'] ?? $defaultStatus?->id,
            'assigned_user_id' => $data['assigned_user_id'],
            'inquiry_date' => $data['inquiry_date'] ?? today(),
            'next_followup_at' => $nextFollowupAt,
            'last_activity_at' => now(),
        ]);

        if ($nextFollowupAt) {
            CrmFollowup::create([
                'lead_id' => $lead->id,
                'status' => 'pending',
                'followup_at' => $nextFollowupAt,
                'note' => $data['note'] ?? 'First follow-up',
                'assigned_user_id' => $lead->assigned_user_id,
'created_by' => auth()->id(),
            ]);
        }

        CrmActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'lead_created',
            'description' => 'Lead created manually.',
        ]);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created successfully.');
    }
}
