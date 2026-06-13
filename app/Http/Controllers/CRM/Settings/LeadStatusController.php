<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmLeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function index()
    {
        $items = CrmLeadStatus::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('crm.settings.lead-statuses.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_default' => 'nullable|boolean',
            'is_final' => 'nullable|boolean',
            'is_admission' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            CrmLeadStatus::query()->update(['is_default' => false]);
        }

        CrmLeadStatus::create([
            ...$data,
            'color' => $data['color'] ?? 'primary',
            'is_default' => $request->boolean('is_default'),
            'is_final' => $request->boolean('is_final'),
            'is_admission' => $request->boolean('is_admission'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Lead status added successfully.');
    }

    public function update(Request $request, CrmLeadStatus $leadStatus)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_default' => 'nullable|boolean',
            'is_final' => 'nullable|boolean',
            'is_admission' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            CrmLeadStatus::where('id', '!=', $leadStatus->id)->update(['is_default' => false]);
        }

        $leadStatus->update([
            ...$data,
            'color' => $data['color'] ?? 'primary',
            'is_default' => $request->boolean('is_default'),
            'is_final' => $request->boolean('is_final'),
            'is_admission' => $request->boolean('is_admission'),
            'is_active' => $request->boolean('is_active', false),
        ]);

        return back()->with('success', 'Lead status updated successfully.');
    }

    public function destroy(CrmLeadStatus $leadStatus)
    {
        $leadStatus->delete();
        return back()->with('success', 'Lead status deleted successfully.');
    }
}
