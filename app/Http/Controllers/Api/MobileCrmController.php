<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmCall;
use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmWhatsappTemplate;
use App\Models\CRM\CrmWhatsappLog;

class MobileCrmController extends Controller
{
    public function appSettings()
    {
        $settings = Schema::hasTable('crm_app_settings')
            ? DB::table('crm_app_settings')->first()
            : null;

        return response()->json([
            'success' => true,
            'message' => 'App settings fetched successfully',
            'data' => [
                'app_name' => $settings->app_name ?? "Turnkey WACRM",
                'logo_url' => $this->fileUrl($settings->app_logo ?? null),
                'login_logo_url' => $this->fileUrl($settings->login_logo ?? null),
                'splash_logo_url' => $this->fileUrl($settings->splash_logo ?? null),
                'default_profile_image_url' => $this->fileUrl($settings->default_profile_image ?? null),
            ],
        ]);
    }

    public function courses(Request $request)
    {
        if (!Schema::hasTable('crm_courses')) {
            return response()->json([
                'success' => true,
                'message' => 'Courses table not found',
                'data' => [],
            ]);
        }

        $courses = DB::table('crm_courses')
            ->leftJoin('crm_standards', 'crm_standards.id', '=', 'crm_courses.standard_id')
            ->select(
                'crm_courses.id',
                'crm_courses.name',
                'crm_courses.standard_id',
                'crm_courses.fee_amount',
                'crm_courses.description',
                'crm_standards.name as standard_name'
            )
            ->where('crm_courses.is_active', 1)
            ->orderBy('crm_courses.sort_order')
            ->orderBy('crm_courses.name')
            ->get()
            ->map(function ($course) {
                $template = null;

                if (Schema::hasTable('crm_whatsapp_templates')) {
                    $template = DB::table('crm_whatsapp_templates')
                        ->where('is_active', 1)
                        ->where(function ($query) use ($course) {
                            $query->where('course_id', $course->id)
                                ->orWhereNull('course_id');
                        })
                        ->orderBy('sort_order')
                        ->orderBy('title')
                        ->first();
                }

                return [
                    'id' => $course->id,
                    'title' => $course->name,
                    'name' => $course->name,
                    'standard_id' => $course->standard_id,
                    'standard' => $course->standard_name,
                    'fee_amount' => $course->fee_amount ?? 0,
                    'description' => $course->description ?? '',
                    'message' => $this->replaceUserTemplateVariables(
                        $template->message ?? 'Hello! Thank you for contacting us.',
                        $request->user()
                    ),
                    'template_id' => $template->id ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Courses fetched successfully',
            'data' => $courses,
        ]);
    }

    public function whatsappTemplates(Request $request)
    {
        if (!Schema::hasTable('crm_whatsapp_templates')) {
            return response()->json([
                'success' => true,
                'message' => 'WhatsApp templates table not found',
                'data' => [],
            ]);
        }

        $templates = DB::table('crm_whatsapp_templates')
            ->leftJoin('crm_courses', 'crm_courses.id', '=', 'crm_whatsapp_templates.course_id')
            ->select(
                'crm_whatsapp_templates.id',
                'crm_whatsapp_templates.course_id',
                'crm_courses.name as course_name',
                'crm_whatsapp_templates.title',
                'crm_whatsapp_templates.message'
            )
            ->where('crm_whatsapp_templates.is_active', 1)
            ->when($request->filled('course_id'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('crm_whatsapp_templates.course_id', $request->course_id)
                        ->orWhereNull('crm_whatsapp_templates.course_id');
                });
            })
            ->orderBy('crm_whatsapp_templates.sort_order')
            ->orderBy('crm_whatsapp_templates.title')
            ->get()
            ->map(function ($template) use ($request) {
                $template->message = $this->replaceUserTemplateVariables($template->message, $request->user());

                return $template;
            });

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp templates fetched successfully',
            'data' => $templates,
        ]);
    }

    public function serverCalls(Request $request)
    {
        if (!Schema::hasTable('crm_calls')) {
            return response()->json([
                'success' => true,
                'message' => 'Calls table not found',
                'data' => [],
            ]);
        }

        $query = DB::table('crm_calls')
            ->leftJoin('crm_courses', 'crm_courses.id', '=', 'crm_calls.course_id')
            ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_calls.lead_id')
            ->select(
                'crm_calls.*',
                'crm_courses.name as course_name',
                'crm_leads.lead_no',
                'crm_leads.parent_name as lead_parent_name',
                'crm_leads.student_name as lead_student_name'
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('crm_calls.phone', 'like', $search)
                        ->orWhere('crm_calls.caller_name', 'like', $search);
                });
            });

        if (($request->user()->role ?? 'admin') !== 'admin') {
            $query->where('crm_calls.user_id', $request->user()->id);
        }

        $calls = $query
            ->orderByDesc('crm_calls.received_at')
            ->orderByDesc('crm_calls.id')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'message' => 'Call logs fetched successfully',
            'data' => $calls->getCollection()->map(function ($call) {
                return $this->formatCall($call);
            })->values(),
            'meta' => [
                'current_page' => $calls->currentPage(),
                'last_page' => $calls->lastPage(),
                'total' => $calls->total(),
            ],
        ]);
    }
    
    public function storeCall(Request $request)
{
    $data = $request->validate([
        'phone' => ['required', 'string', 'max:30'],
        'name' => ['nullable', 'string', 'max:255'],
        'caller_name' => ['nullable', 'string', 'max:255'],
        'course_id' => ['nullable', 'integer', 'exists:crm_courses,id'],
        'status_id' => ['nullable', 'integer', 'exists:crm_courses,id'],
        'call_type' => ['nullable', 'string', 'max:50'],
        'received_at' => ['nullable', 'date'],
    ]);

    $phone = preg_replace('/\s+/', '', $data['phone']);

    $courseId = $data['course_id'] ?? $data['status_id'] ?? null;

    $callerName = $data['caller_name'] ?? $data['name'] ?? null;

    $lead = CrmLead::where('phone', $phone)
        ->orWhere('mobile', $phone)
        ->latest()
        ->first();

    $call = new CrmCall();
    $call->phone = $phone;
    $call->caller_name = $callerName;
    $call->call_type = $data['call_type'] ?? 'mobile_call';
    $call->received_at = !empty($data['received_at'])
        ? \Carbon\Carbon::parse($data['received_at'])
        : now();
    $call->lead_id = optional($lead)->id;
    $call->user_id = optional($request->user())->id;
    $call->course_id = $courseId;
    $call->status = $courseId ? 'course_assigned' : 'new';
    $call->notes = 'Call synced from mobile app.';
    $call->save();

    return response()->json([
        'success' => true,
        'message' => 'Call saved successfully.',
        'data' => $this->formatCall($call->fresh(['course', 'lead'])),
    ], 201);
}
    
    public function updateCallCourse(Request $request, CrmCall $call)
{
    $data = $request->validate([
        'course_id' => ['nullable', 'integer', 'exists:crm_courses,id'],
        'status_id' => ['nullable', 'integer', 'exists:crm_courses,id'],
        'remark' => ['nullable', 'string', 'max:5000'],
    ]);

    $courseId = $data['course_id'] ?? $data['status_id'] ?? null;

    if (!$courseId) {
        return response()->json([
            'success' => false,
            'message' => 'Course is required.',
        ], 422);
    }

    $call->update([
        'course_id' => $courseId,
        'status' => 'course_assigned',
        'notes' => $data['remark'] ?? $call->notes,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Course selected successfully.',
        'data' => $this->formatCall($call->fresh(['course', 'lead'])),
    ]);
}

    
   public function logWhatsapp(Request $request)
{
    $data = $request->validate([
        'call_id' => ['nullable', 'integer', 'exists:crm_calls,id'],
        'phone' => ['required', 'string', 'max:25'],
        'name' => ['nullable', 'string', 'max:255'],
        'course_id' => ['required', 'integer', 'exists:crm_courses,id'],
        'message' => ['nullable', 'string', 'max:5000'],
        'sent_at' => ['nullable', 'date'],
    ]);

    $phone = preg_replace('/\s+/', '', $data['phone']);
    $sentAt = !empty($data['sent_at']) ? Carbon::parse($data['sent_at']) : now();

    /*
    |--------------------------------------------------------------------------
    | Get WhatsApp Template Message For Selected Course
    |--------------------------------------------------------------------------
    */
    $template = $this->courseTemplate($data['course_id']);

    $message = $data['message'] ?? null;

    if (
        !$message ||
        trim($message) === '' ||
        trim($message) === 'Hello! Thank you for contacting us.'
    ) {
        $message = optional($template)->message ?? 'Hello! Thank you for contacting us.';
    }

    $message = $this->replaceUserTemplateVariables($message, $request->user());

    /*
    |--------------------------------------------------------------------------
    | Find Existing Lead
    |--------------------------------------------------------------------------
    */
    $lead = null;

    if (Schema::hasTable('crm_leads')) {
        $lead = DB::table('crm_leads')
            ->where(function ($query) use ($phone) {
                $query->where('phone', $phone)
                    ->orWhere('mobile', $phone);
            })
            ->orderByDesc('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Existing Call If call_id Is Coming, Otherwise Create New Call
    |--------------------------------------------------------------------------
    */
    $callId = $data['call_id'] ?? null;

    if (Schema::hasTable('crm_calls')) {
        if ($callId) {
            DB::table('crm_calls')
                ->where('id', $callId)
                ->update([
                    'phone' => $phone,
                    'caller_name' => $data['name'] ?? DB::raw('caller_name'),
                    'lead_id' => $lead->id ?? null,
                    'user_id' => optional($request->user())->id,
                    'course_id' => $data['course_id'],
                    'whatsapp_sent_at' => $sentAt,
                    'status' => 'whatsapp_sent',
                    'notes' => 'Course assigned and WhatsApp message sent from mobile app.',
                    'updated_at' => now(),
                ]);
        } else {
            $callId = DB::table('crm_calls')->insertGetId([
                'phone' => $phone,
                'caller_name' => $data['name'] ?? null,
                'call_type' => 'mobile_whatsapp',
                'received_at' => $sentAt,
                'lead_id' => $lead->id ?? null,
                'user_id' => optional($request->user())->id,
                'course_id' => $data['course_id'],
                'whatsapp_sent_at' => $sentAt,
                'status' => 'whatsapp_sent',
                'notes' => 'Course assigned and WhatsApp message sent from mobile app.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save WhatsApp Log With Correct Template Message
    |--------------------------------------------------------------------------
    */
    $whatsappId = null;

    if (Schema::hasTable('crm_whatsapp_logs')) {
        $whatsappId = DB::table('crm_whatsapp_logs')->insertGetId([
            'phone' => $phone,
            'parent_name' => $data['name'] ?? null,
            'message' => $message,
            'status' => 'sent',
            'sent_at' => $sentAt,
            'lead_id' => $lead->id ?? null,
            'user_id' => optional($request->user())->id,
            'course_id' => $data['course_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Return Updated Call With Course + Template Message
    |--------------------------------------------------------------------------
    */
    $call = null;

    if ($callId) {
        $call = DB::table('crm_calls')
            ->leftJoin('crm_courses', 'crm_courses.id', '=', 'crm_calls.course_id')
            ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_calls.lead_id')
            ->select(
                'crm_calls.*',
                'crm_courses.name as course_name',
                'crm_leads.lead_no',
                'crm_leads.parent_name as lead_parent_name',
                'crm_leads.student_name as lead_student_name'
            )
            ->where('crm_calls.id', $callId)
            ->first();
    }

    return response()->json([
        'success' => true,
        'message' => 'WhatsApp record synced to CRM successfully.',
        'data' => [
            'call' => $call ? $this->formatCall($call) : null,
            'call_id' => $callId,
            'whatsapp_log_id' => $whatsappId,
            'template_id' => optional($template)->id ? (int) optional($template)->id : null,
            'message' => $message,
        ],
    ], 201);
}

    public function leads(Request $request)
    {
        if (!Schema::hasTable('crm_leads')) {
            return response()->json([
                'success' => true,
                'message' => 'Leads table not found',
                'data' => [],
            ]);
        }

        $query = DB::table('crm_leads')
            ->leftJoin('crm_courses', 'crm_courses.id', '=', 'crm_leads.course_id')
            ->leftJoin('crm_standards', 'crm_standards.id', '=', 'crm_leads.standard_id')
            ->leftJoin('crm_lead_statuses', 'crm_lead_statuses.id', '=', 'crm_leads.lead_status_id')
            ->leftJoin('crm_lead_priorities', 'crm_lead_priorities.id', '=', 'crm_leads.lead_priority_id')
            ->leftJoin('crm_lead_sources', 'crm_lead_sources.id', '=', 'crm_leads.lead_source_id')
            ->leftJoin('users', 'users.id', '=', 'crm_leads.assigned_user_id')
            ->select(
                'crm_leads.*',
                'crm_courses.name as course_name',
                'crm_standards.name as standard_name',
                'crm_lead_statuses.name as status_name',
                'crm_lead_statuses.color as status_color',
                'crm_lead_priorities.name as priority_name',
                'crm_lead_priorities.color as priority_color',
                'crm_lead_sources.name as source_name',
                'users.name as assigned_user_name'
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('crm_leads.phone', 'like', $search)
                        ->orWhere('crm_leads.mobile', 'like', $search)
                        ->orWhere('crm_leads.parent_name', 'like', $search)
                        ->orWhere('crm_leads.student_name', 'like', $search)
                        ->orWhere('crm_leads.lead_no', 'like', $search);
                });
            });

        if (($request->user()->role ?? 'admin') !== 'admin') {
            $query->where('crm_leads.assigned_user_id', $request->user()->id);
        }

        $leads = $query
            ->orderByDesc('crm_leads.id')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'message' => 'Leads fetched successfully',
            'data' => $leads->getCollection()->map(function ($lead) {
                return $this->formatLead($lead);
            })->values(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function followups(Request $request)
{
    if (!Schema::hasTable('crm_followups')) {
        return response()->json([
            'success' => true,
            'message' => 'Follow-ups table not found',
            'data' => [],
        ]);
    }

    $type = $request->get('type', 'all');

    $query = DB::table('crm_followups')
        ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_followups.lead_id')
        ->leftJoin('crm_courses', 'crm_courses.id', '=', 'crm_leads.course_id')
        ->leftJoin('crm_standards', 'crm_standards.id', '=', 'crm_leads.standard_id')
        ->leftJoin('crm_followup_types', 'crm_followup_types.id', '=', 'crm_followups.followup_type_id')
        ->leftJoin('users', 'users.id', '=', 'crm_followups.assigned_user_id')
        ->select(
            'crm_followups.*',
            'crm_leads.lead_no',
            'crm_leads.parent_name',
            'crm_leads.student_name',
            'crm_leads.phone',
            'crm_leads.mobile',
            'crm_courses.name as course_name',
            'crm_standards.name as standard_name',
            'crm_followup_types.name as followup_type_name',
            'users.name as created_by_name'
        );

    if (Schema::hasColumn('crm_leads', 'is_closed')) {
        $query->where(function ($q) {
            $q->where('crm_leads.is_closed', 0)
                ->orWhereNull('crm_leads.is_closed');
        });
    }

    if (($request->user()->role ?? 'admin') !== 'admin') {
        $query->where('crm_leads.assigned_user_id', $request->user()->id);
    }

    if ($type === 'today') {
        $query->whereDate('crm_followups.followup_at', today())
            ->where('crm_followups.status', 'pending');
    } elseif ($type === 'pending') {
        $query->where('crm_followups.status', 'pending');
    } elseif ($type === 'overdue') {
        $query->where('crm_followups.status', 'pending')
            ->where('crm_followups.followup_at', '<', now());
    } elseif ($type === 'completed') {
        $query->where('crm_followups.status', 'completed');
    }

    $followups = $query
        ->orderByDesc('crm_followups.followup_at')
        ->orderByDesc('crm_followups.id')
        ->paginate(30);

    return response()->json([
        'success' => true,
        'message' => 'Follow-ups fetched successfully',
        'data' => $followups->getCollection()->map(function ($followup) {
            return $this->formatFollowup($followup);
        })->values(),
        'meta' => [
            'current_page' => $followups->currentPage(),
            'last_page' => $followups->lastPage(),
            'total' => $followups->total(),
        ],
    ]);
}

    public function completeFollowup($followup)
    {
        if (!Schema::hasTable('crm_followups')) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-ups table not found',
            ], 404);
        }

        DB::table('crm_followups')
            ->where('id', $followup)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up marked as completed.',
        ]);
    }

    public function masters()
    {
        return response()->json([
            'success' => true,
            'message' => 'Masters fetched successfully',
            'data' => [
                'standards' => $this->activeMaster('crm_standards'),
                'courses' => $this->activeMaster('crm_courses'),
                'statuses' => $this->activeMaster('crm_lead_statuses'),
                'lead_statuses' => $this->activeMaster('crm_lead_statuses'),
                'sources' => $this->activeMaster('crm_lead_sources'),
                'lead_sources' => $this->activeMaster('crm_lead_sources'),
                'priorities' => $this->activeMaster('crm_lead_priorities'),
                'lead_priorities' => $this->activeMaster('crm_lead_priorities'),
                'followup_types' => $this->activeMaster('crm_followup_types'),
            ],
        ]);
    }

    private function activeMaster(string $table)
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return DB::table($table)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

   private function formatCall($call): array
{
    $courseId = $call->course_id ?? null;

    $courseName = $call->course_name ?? null;

    if (!$courseName && isset($call->course) && $call->course) {
        $courseName = $call->course->name;
    }

    $template = $this->courseTemplate($courseId);

    $message = $this->replaceUserTemplateVariables(
        optional($template)->message ?? 'Hello! Thank you for contacting us.',
        request()->user()
    );

    $courseData = $courseId ? [
        'id' => (int) $courseId,
        'title' => $courseName ?? 'Selected Course',
        'name' => $courseName ?? 'Selected Course',
        'message' => $message,
        'template_id' => optional($template)->id ? (int) optional($template)->id : null,
    ] : null;

    return [
        'id' => (int) $call->id,
        'phone' => $call->phone,
        'name' => $call->caller_name,

        'course_id' => $courseId ? (int) $courseId : null,
        'course' => $courseName ?? 'Select Course',

        // Direct message for mobile
        'message' => $message,
        'template_id' => optional($template)->id ? (int) optional($template)->id : null,

        // Important for old mobile parsing
        'course_data' => $courseData,
        'status' => $courseData,
        'latestFollowup' => $courseData ? [
            'id' => (int) $call->id,
            'status' => $courseData,
            'remark' => $call->notes ?? null,
        ] : null,

        'time' => !empty($call->received_at)
            ? Carbon::parse($call->received_at)->diffForHumans()
            : (!empty($call->created_at) ? Carbon::parse($call->created_at)->diffForHumans() : ''),

        'created_at' => !empty($call->created_at)
            ? Carbon::parse($call->created_at)->format('Y-m-d H:i:s')
            : null,

        'duration' => 'Synced',
        'whatsapp_sent' => !empty($call->whatsapp_sent_at),

        'lead' => !empty($call->lead_id) ? [
            'id' => (int) $call->lead_id,
            'lead_no' => $call->lead_no ?? null,
            'parent_name' => $call->lead_parent_name ?? null,
            'student_name' => $call->lead_student_name ?? null,
        ] : null,
    ];
}

private function courseTemplate($courseId)
{
    if (!$courseId || !Schema::hasTable('crm_whatsapp_templates')) {
        return null;
    }

    return CrmWhatsappTemplate::where('is_active', true)
        ->where(function ($query) use ($courseId) {
            $query->where('course_id', $courseId)
                ->orWhereNull('course_id');
        })
        ->orderByRaw('course_id IS NULL')
        ->orderBy('sort_order')
        ->orderBy('title')
        ->first();
}

    private function replaceUserTemplateVariables(?string $message, $user): string
    {
        $message = $message ?? 'Hello! Thank you for contacting us.';

        $replacements = [
            '{user_name}' => $user->name ?? '',
            '{username}' => $user->name ?? '',
            '{user_mobile}' => $user->mobile ?? '',
            '{mobile_number}' => $user->mobile ?? '',
        ];

        return strtr($message, $replacements);
    }

    private function formatLead($lead): array
    {
        return [
            'id' => $lead->id,
            'lead_no' => $lead->lead_no,
            'parent_name' => $lead->parent_name,
            'student_name' => $lead->student_name,
            'phone' => $lead->phone ?? $lead->mobile,
            'mobile' => $lead->mobile ?? $lead->phone,
            'course' => $lead->course_name,
            'standard' => $lead->standard_name,
            'status' => $lead->status_name,
            'status_color' => $lead->status_color,
            'priority' => $lead->priority_name,
            'priority_color' => $lead->priority_color,
            'source' => $lead->source_name,
            'assigned_user' => $lead->assigned_user_name,
            'next_followup_at' => $lead->next_followup_at
                ? Carbon::parse($lead->next_followup_at)->format('d M Y h:i A')
                : null,
            'created_at' => $lead->created_at
                ? Carbon::parse($lead->created_at)->format('d M Y')
                : null,
        ];
    }

    private function formatFollowup($followup): array
    {
        return [
            'id' => $followup->id,
            'lead_id' => $followup->lead_id,
            'lead_no' => $followup->lead_no,
            'parent_name' => $followup->parent_name,
            'student_name' => $followup->student_name,
            'phone' => $followup->phone ?? $followup->mobile,
            'mobile' => $followup->mobile ?? $followup->phone,
            'course' => $followup->course_name,
            'standard' => $followup->standard_name,
            'followup_type' => $followup->followup_type_name,
            'status' => $followup->status,
            'note' => $followup->note,
            'followup_at' => $followup->followup_at
                ? Carbon::parse($followup->followup_at)->format('d M Y h:i A')
                : null,
            'next_followup_at' => $followup->next_followup_at
                ? Carbon::parse($followup->next_followup_at)->format('d M Y h:i A')
                : null,
            'created_by' => $followup->created_by_name,
        ];
    }

    private function fileUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
    
    public function dashboardStats(Request $request)
{
    if (!Schema::hasTable('crm_leads')) {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard stats fetched successfully',
            'data' => [
                'today' => [
                    'leads' => 0,
                    'followups' => 0,
                    'pending_followups' => 0,
                    'closed_leads' => 0,
                    'next_followups' => 0,
                ],
                'all' => [
                    'leads' => 0,
                    'followups' => 0,
                    'pending_followups' => 0,
                    'closed_leads' => 0,
                    'next_followups' => 0,
                ],
                'updated_at' => now()->format('d M Y h:i A'),
            ],
        ]);
    }

    $user = $request->user();
    $hasClosedColumn = Schema::hasColumn('crm_leads', 'is_closed');
    $hasClosedAtColumn = Schema::hasColumn('crm_leads', 'closed_at');

    $activeLeads = function () use ($user, $hasClosedColumn) {
        $query = DB::table('crm_leads');

        if ($hasClosedColumn) {
            $query->where(function ($q) {
                $q->where('is_closed', 0)
                    ->orWhereNull('is_closed');
            });
        }

        if (($user->role ?? 'admin') !== 'admin') {
            $query->where('assigned_user_id', $user->id);
        }

        return $query;
    };

    $closedLeads = function () use ($user, $hasClosedColumn) {
        $query = DB::table('crm_leads');

        if ($hasClosedColumn) {
            $query->where('is_closed', 1);
        } else {
            $query->whereRaw('1 = 0');
        }

        if (($user->role ?? 'admin') !== 'admin') {
            $query->where('assigned_user_id', $user->id);
        }

        return $query;
    };

    $activeFollowups = function () use ($user, $hasClosedColumn) {
        $query = DB::table('crm_followups')
            ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_followups.lead_id');

        if ($hasClosedColumn) {
            $query->where(function ($q) {
                $q->where('crm_leads.is_closed', 0)
                    ->orWhereNull('crm_leads.is_closed');
            });
        }

        if (($user->role ?? 'admin') !== 'admin') {
            $query->where('crm_leads.assigned_user_id', $user->id);
        }

        return $query;
    };

    $todayClosedQuery = $closedLeads();

    if ($hasClosedAtColumn) {
        $todayClosedQuery->whereDate('closed_at', today());
    } else {
        $todayClosedQuery->whereDate('updated_at', today());
    }

    return response()->json([
        'success' => true,
        'message' => 'Dashboard stats fetched successfully',
        'data' => [
            'today' => [
                'leads' => $activeLeads()->whereDate('created_at', today())->count(),

                'followups' => $activeFollowups()
                    ->whereDate('crm_followups.followup_at', today())
                    ->count(),

                'pending_followups' => $activeFollowups()
                    ->whereDate('crm_followups.followup_at', today())
                    ->where('crm_followups.status', 'pending')
                    ->count(),

                'closed_leads' => $todayClosedQuery->count(),

                'next_followups' => $activeFollowups()
                    ->whereDate('crm_followups.followup_at', today())
                    ->where('crm_followups.status', 'pending')
                    ->where('crm_followups.followup_at', '>=', now())
                    ->count(),
            ],

            'all' => [
                'leads' => $activeLeads()->count(),

                'followups' => $activeFollowups()->count(),

                'pending_followups' => $activeFollowups()
                    ->where('crm_followups.status', 'pending')
                    ->count(),

                'closed_leads' => $closedLeads()->count(),

                'next_followups' => $activeFollowups()
                    ->where('crm_followups.status', 'pending')
                    ->where('crm_followups.followup_at', '>=', now())
                    ->count(),
            ],

            'updated_at' => now()->format('d M Y h:i A'),
        ],
    ]);
}
}
