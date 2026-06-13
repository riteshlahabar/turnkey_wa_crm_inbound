<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmWhatsappTemplate;
use Illuminate\Http\Request;

class WhatsappTemplateController extends Controller
{
    public function index()
    {
        $courses = CrmCourse::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $templates = CrmWhatsappTemplate::with('course')
            ->when(request('course_id'), function ($query) {
                $query->where('course_id', request('course_id'));
            })
            ->orderBy('sort_order')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.settings.whatsapp-templates.index', compact('courses', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'nullable|exists:crm_courses,id',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        CrmWhatsappTemplate::create($data);

        return back()->with('success', 'WhatsApp template added successfully.');
    }

    public function update(Request $request, CrmWhatsappTemplate $whatsappTemplate)
    {
        $data = $request->validate([
            'course_id' => 'nullable|exists:crm_courses,id',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $whatsappTemplate->update($data);

        return back()->with('success', 'WhatsApp template updated successfully.');
    }

    public function destroy(CrmWhatsappTemplate $whatsappTemplate)
    {
        $whatsappTemplate->delete();

        return back()->with('success', 'WhatsApp template deleted successfully.');
    }

    public function apiTemplates(Request $request)
    {
        $templates = CrmWhatsappTemplate::with('course:id,name')
            ->where('is_active', true)
            ->when($request->filled('course_id'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('course_id', $request->course_id)
                        ->orWhereNull('course_id');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'course_id' => $template->course_id,
                    'course_name' => optional($template->course)->name,
                    'title' => $template->title,
                    'message' => $template->message,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}
