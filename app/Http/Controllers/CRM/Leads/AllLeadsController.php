<?php

namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\Concerns\CrmFormOptions;
use App\Models\CRM\CrmActivity;
use App\Models\CRM\CrmCall;
use App\Models\CRM\CrmFeeQuotation;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmLeadSource;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmWhatsappLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AllLeadsController extends Controller
{
    use CrmFormOptions;

    public function index(Request $request)
{
    $leads = CrmLead::with(['source', 'status', 'priority', 'standard', 'course', 'assignedUser'])
        ->forLoggedInUser()
        ->where(function ($q) {
            $q->where('is_closed', false)
                ->orWhereNull('is_closed');
        })
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('lead_no', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('status_id'), fn ($q) => $q->where('lead_status_id', $request->status_id))
        ->when($request->filled('source_id'), fn ($q) => $q->where('lead_source_id', $request->source_id))
        ->when($request->filled('assigned_user_id'), fn ($q) => $q->where('assigned_user_id', $request->assigned_user_id))
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view('crm.leads.all-leads.index', array_merge($this->formOptions(), compact('leads')));
}

public function closed(Request $request)
{
    if ($request->get('export') === 'excel') {
        return $this->exportClosedLeads($request);
    }

    $leads = $this->closedLeadQuery($request)
        ->latest('closed_at')
        ->paginate(20)
        ->withQueryString();

    return view('crm.leads.closed-leads.index', array_merge(
        $this->formOptions(),
        compact('leads')
    ));
}

private function closedLeadQuery(Request $request)
{
    return CrmLead::with([
        'source',
        'status',
        'closedStatus',
        'priority',
        'standard',
        'course',
        'assignedUser',
        'closedBy',
        'quotations.course',
        'followups' => function ($query) {
            $query->with(['type', 'assignedUser', 'creator'])
                ->orderByDesc('followup_at')
                ->orderByDesc('id');
        },
    ])
        ->forLoggedInUser()
        ->where('is_closed', true)
        ->when($request->filled('from_date'), function ($query) use ($request) {
            $query->whereDate('closed_at', '>=', $request->from_date);
        })
        ->when($request->filled('to_date'), function ($query) use ($request) {
            $query->whereDate('closed_at', '<=', $request->to_date);
        })
        ->when($request->filled('status_id'), fn ($q) => $q->where('lead_status_id', $request->status_id))
        ->when($request->filled('assigned_user_id'), fn ($q) => $q->where('assigned_user_id', $request->assigned_user_id))
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('lead_no', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhereHas('standard', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        });
}

private function exportClosedLeads(Request $request)
{
    $leads = $this->closedLeadQuery($request)
        ->latest('closed_at')
        ->get();

    $fileName = 'closed-leads-' . now()->format('Y-m-d-H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function () use ($leads) {
        $file = fopen('php://output', 'w');

        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'Lead No',
            'Student Name',
            'Parent Name',
            'Phone',
            'Level',
            'Course',
            'Source',
            'Status',
            'Priority',
            'Fee',
            'Assigned User',
            'Closed Date',
            'Closed By',
            'Closed Note',
        ]);

        foreach ($leads as $lead) {
            $quotation = $lead->quotations?->sortByDesc('created_at')->first();

            $fee = $quotation?->final_amount
                ?? $quotation?->final_fee
                ?? $quotation?->fee_amount
                ?? $quotation?->total_fee
                ?? '';

            fputcsv($file, [
                $lead->lead_no,
                $lead->student_name,
                $lead->parent_name,
                $lead->phone ?? $lead->mobile,
                $lead->standard?->name,
                $lead->course?->name,
                $lead->source?->name,
                $lead->status?->name,
                $lead->priority?->name,
                $fee,
                $lead->assignedUser?->name,
                optional($lead->closed_at)->format('d M Y h:i A'),
                $lead->closedBy?->name,
                $lead->closed_note,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    public function show(CrmLead $lead)
    {
        $lead->load([
            'source', 'status', 'priority', 'standard', 'course', 'assignedUser',
            'followups.type', 'followups.creator', 'calls', 'whatsappLogs', 'quotations.course', 'activities.user',
        ]);

        return view('crm.leads.all-leads.show', array_merge($this->formOptions(), compact('lead')));
    }

    public function edit(CrmLead $lead)
    {
        return view('crm.leads.all-leads.edit', array_merge($this->formOptions(), compact('lead')));
    }

    public function update(Request $request, CrmLead $lead)
    {
        $data = $request->validate([
            'parent_name' => 'nullable|string|max:255',
            'student_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'alternate_mobile' => 'nullable|string|max:20',
            'standard_id' => 'nullable|exists:crm_standards,id',
            'course_id' => 'nullable|exists:crm_courses,id',
            'school_name' => 'nullable|string|max:255',
            'board' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'lead_source_id' => 'nullable|exists:crm_lead_sources,id',
            'lead_status_id' => 'nullable|exists:crm_lead_statuses,id',
            'lead_priority_id' => 'nullable|exists:crm_lead_priorities,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'inquiry_date' => 'nullable|date',
            'next_followup_date' => 'nullable|date',
            'next_followup_time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string',
        ]);

        if (!empty($data['next_followup_date'])) {
            $data['next_followup_at'] = $data['next_followup_date'] . ' ' . ($data['next_followup_time'] ?? '10:00');
        }
        unset($data['next_followup_date'], $data['next_followup_time']);

        $oldStatusId = $lead->lead_status_id;
        $lead->update([...$data, 'last_activity_at' => now()]);

        if ($oldStatusId != $lead->lead_status_id) {
            $status = CrmLeadStatus::find($lead->lead_status_id);
            if ($status?->is_admission && !$lead->admission_done_at) {
                $lead->update(['admission_done_at' => now()]);
            }
            CrmActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'status_changed',
                'description' => 'Lead status changed to ' . ($status->name ?? 'N/A') . '.',
            ]);
        }

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(CrmLead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function bulkAction(Request $request)
{
    \Log::info('BULK_ACTION_HIT', [
    'url' => $request->fullUrl(),
    'method' => $request->method(),
    'all' => $request->all(),
]);
    $request->validate([
        'bulk_action' => ['required', 'string'],
        'lead_ids' => ['required', 'array'],
        'lead_ids.*' => ['integer', 'exists:crm_leads,id'],
        'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
        'lead_status_id' => ['nullable', 'integer', 'exists:crm_lead_statuses,id'],
        'lead_priority_id' => ['nullable', 'integer', 'exists:crm_lead_priorities,id'],
    ]);

    $leadIds = $request->input('lead_ids', []);
    $action = $request->input('bulk_action');
    if ($action === 'export_csv') {
    return $this->exportBulkCsv($request);
}

    if (empty($leadIds)) {
        return redirect()
            ->route('leads.index')
            ->with('error', 'Please select at least one lead.');
    }

    if ($action === 'assign') {
        if (!$request->filled('assigned_user_id')) {
            return redirect()
                ->route('leads.index')
                ->with('error', 'Please select user.');
        }

        CrmLead::whereIn('id', $leadIds)->update([
            'assigned_user_id' => $request->assigned_user_id,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Selected leads assigned successfully.');
    }

    if ($action === 'status') {
        if (!$request->filled('lead_status_id')) {
            return redirect()
                ->route('leads.index')
                ->with('error', 'Please select status.');
        }

        CrmLead::whereIn('id', $leadIds)->update([
            'lead_status_id' => $request->lead_status_id,
            'last_activity_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Selected leads status updated successfully.');
    }

    if ($action === 'priority') {
        if (!$request->filled('lead_priority_id')) {
            return redirect()
                ->route('leads.index')
                ->with('error', 'Please select priority.');
        }

        CrmLead::whereIn('id', $leadIds)->update([
            'lead_priority_id' => $request->lead_priority_id,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Selected leads priority updated successfully.');
    }

    return redirect()
        ->route('leads.index')
        ->with('error', 'Invalid bulk action selected.');
}

    public function convertFromCall(CrmCall $call)
    {
        return DB::transaction(function () use ($call) {
            $source = CrmLeadSource::firstOrCreate(['name' => 'Call'], ['sort_order' => 1, 'is_active' => true]);
            $status = CrmLeadStatus::where('is_default', true)->first() ?? CrmLeadStatus::first();

            $lead = CrmLead::firstOrCreate(
                ['phone' => $call->phone],
                [
                    'lead_no' => $this->newLeadNumber(),
                    'parent_name' => $call->caller_name,
                    'lead_source_id' => $source->id,
                    'lead_status_id' => $status?->id,
                    'assigned_user_id' => auth()->id(),
                    'inquiry_date' => today(),
                    'last_activity_at' => now(),
                    'note' => 'Converted from call record.',
                ]
            );

            $call->update(['lead_id' => $lead->id, 'status' => 'converted', 'user_id' => auth()->id()]);

            CrmActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'call_converted',
                'description' => 'Call converted to lead.',
            ]);

            return redirect()->route('leads.show', $lead)->with('success', 'Call converted to lead successfully.');
        });
    }

    public function convertFromWhatsapp(CrmWhatsappLog $whatsapp)
    {
        return DB::transaction(function () use ($whatsapp) {
            $source = CrmLeadSource::firstOrCreate(['name' => 'WhatsApp'], ['sort_order' => 2, 'is_active' => true]);
            $status = CrmLeadStatus::where('is_default', true)->first() ?? CrmLeadStatus::first();

            $lead = CrmLead::firstOrCreate(
                ['phone' => $whatsapp->phone],
                [
                    'lead_no' => $this->newLeadNumber(),
                    'parent_name' => $whatsapp->parent_name,
                    'lead_source_id' => $source->id,
                    'lead_status_id' => $status?->id,
                    'assigned_user_id' => auth()->id(),
                    'inquiry_date' => today(),
                    'last_activity_at' => now(),
                    'note' => 'Converted from WhatsApp record.',
                ]
            );

            $whatsapp->update(['lead_id' => $lead->id, 'user_id' => auth()->id()]);

            CrmActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'whatsapp_converted',
                'description' => 'WhatsApp record converted to lead.',
            ]);

            return redirect()->route('leads.show', $lead)->with('success', 'WhatsApp record converted to lead successfully.');
        });
    }

   public function storeQuotation(Request $request, CrmLead $lead)
{
    $data = $request->validate([
        'course_id' => 'nullable|exists:crm_courses,id',
        'total_fee' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
        'installment_note' => 'nullable|string',
        'note' => 'nullable|string',
    ]);

    $feeAmount = $data['total_fee'];
    $discountAmount = $data['discount'] ?? 0;
    $finalAmount = max(0, $feeAmount - $discountAmount);

    $quotation = CrmFeeQuotation::create([
        'lead_id' => $lead->id,
        'course_id' => $data['course_id'] ?? null,
        'fee_amount' => $feeAmount,
        'discount_amount' => $discountAmount,
        'final_amount' => $finalAmount,
        'note' => $data['installment_note'] ?? $data['note'] ?? null,
        'created_by' => auth()->id(),
    ]);

    CrmActivity::create([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'type' => 'quotation_created',
        'description' => 'Fee quotation created.',
    ]);

    return back()->with('success', 'Fee quotation saved successfully.');
}

    public function printQuotation(CrmFeeQuotation $quotation)
    {
        $quotation->load(['lead', 'course', 'creator']);
        return view('crm.leads.all-leads.quotation-print', compact('quotation'));
    }

    public function admissionForm(CrmLead $lead)
    {
        $lead->load(['standard', 'course', 'assignedUser', 'status', 'quotations']);
        return view('crm.leads.all-leads.admission-form', compact('lead'));
    }
    
    private function exportBulkCsv(Request $request)
{
    $leadIds = $request->input('lead_ids', []);

    if (empty($leadIds)) {
        return back()->with('error', 'Please select at least one lead.');
    }

    $leads = CrmLead::with([
        'status',
        'priority',
        'source',
        'assignedUser',
        'standard',
        'course',
    ])->whereIn('id', $leadIds)->get();

    $fileName = 'leads-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function () use ($leads) {
        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'Lead No',
            'Student Name',
            'Parent Name',
            'Phone',
            'Level',
            'Course',
            'Source',
            'Status',
            'Priority',
            'Next Follow-up',
            'Assigned User',
        ]);

        foreach ($leads as $lead) {
            fputcsv($file, [
                $lead->lead_no,
                $lead->student_name,
                $lead->parent_name,
                $lead->phone,
                $lead->standard?->name,
                $lead->course?->name,
                $lead->source?->name,
                $lead->status?->name,
                $lead->priority?->name,
                optional($lead->next_followup_at)->format('d M Y h:i A'),
                $lead->assignedUser?->name,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
