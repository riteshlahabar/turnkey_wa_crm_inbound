<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmClosedLeadStatus;
use Illuminate\Http\Request;

class ClosedLeadStatusController extends Controller
{
    public function index()
    {
        $statuses = CrmClosedLeadStatus::latest()->paginate(20);

        return view('crm.settings.closed-statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        CrmClosedLeadStatus::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Closed lead status added successfully.');
    }

    public function update(Request $request, CrmClosedLeadStatus $closedStatus)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $closedStatus->update([
            'name' => $data['name'],
            'color' => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Closed lead status updated successfully.');
    }

    public function destroy(CrmClosedLeadStatus $closedStatus)
    {
        $closedStatus->delete();

        return back()->with('success', 'Closed lead status deleted successfully.');
    }
}