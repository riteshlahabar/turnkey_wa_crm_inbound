<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmStandard;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $items = CrmCourse::with('standard')->orderBy('sort_order')->orderBy('name')->paginate(20);
        $standards = CrmStandard::where('is_active', true)->orderBy('sort_order')->get();
        return view('crm.settings.courses.index', compact('items', 'standards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'nullable|exists:crm_standards,id',
            'name' => 'required|string|max:255',
            'fee_amount' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        CrmCourse::create($data);
        return back()->with('success', 'Course added successfully.');
    }

    public function update(Request $request, CrmCourse $course)
    {
        $data = $request->validate([
            'standard_id' => 'nullable|exists:crm_standards,id',
            'name' => 'required|string|max:255',
            'fee_amount' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', false);
        $course->update($data);
        return back()->with('success', 'Course updated successfully.');
    }

    public function destroy(CrmCourse $course)
    {
        $course->delete();
        return back()->with('success', 'Course deleted successfully.');
    }
}
