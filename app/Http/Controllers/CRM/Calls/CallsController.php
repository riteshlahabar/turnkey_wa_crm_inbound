<?php

namespace App\Http\Controllers\CRM\Calls;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCall;
use Illuminate\Http\Request;

class CallsController extends Controller
{
    public function index(Request $request)
    {
        $calls = CrmCall::with(['lead.status', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%")
                      ->orWhere('caller_name', 'like', "%{$search}%");
                });
            })
            ->latest('received_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('crm.calls.index', compact('calls'));
    }

    public function show(CrmCall $call)
    {
        $call->load(['lead.status', 'lead.followups.type', 'user']);
        return view('crm.calls.show', compact('call'));
    }

    public function updateName(Request $request, CrmCall $call)
    {
        $request->validate(['caller_name' => 'required|string|max:255']);
        $call->update(['caller_name' => $request->caller_name]);
        return back()->with('success', 'Caller name updated successfully.');
    }

    public function updateStatus(Request $request, CrmCall $call)
    {
        $request->validate(['status' => 'required|in:new,converted,closed']);
        $call->update(['status' => $request->status]);
        return back()->with('success', 'Call status updated successfully.');
    }

    public function destroy(CrmCall $call)
    {
        $call->delete();
        return back()->with('success', 'Call deleted successfully.');
    }

    public function storeFromMobile(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'caller_name' => 'nullable|string|max:255',
            'call_type' => 'nullable|string|max:50',
            'received_at' => 'nullable|date',
        ]);

        $call = CrmCall::create([
            'phone' => $request->phone,
            'caller_name' => $request->caller_name ?? $request->name,
            'call_type' => $request->call_type ?? 'incoming',
            'received_at' => $request->received_at ?? now(),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call saved successfully.',
            'data' => $call,
        ], 201);
    }

    public function getPendingCalls()
    {
        $calls = CrmCall::whereNull('lead_id')
            ->where('status', 'new')
            ->latest('received_at')
            ->limit(50)
            ->get();

        return response()->json(['success' => true, 'data' => $calls]);
    }
}
