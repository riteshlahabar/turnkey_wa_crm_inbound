<?php

namespace App\Http\Controllers\CRM\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmWhatsappLog;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index(Request $request)
    {
        $logs = CrmWhatsappLog::with(['lead.status', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%")
                      ->orWhere('parent_name', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest('sent_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('crm.whatsapp.index', compact('logs'));
    }

    public function show(CrmWhatsappLog $whatsapp)
    {
        $whatsapp->load(['lead.status', 'lead.followups.type', 'user']);
        return view('crm.whatsapp.show', compact('whatsapp'));
    }

    public function destroy(CrmWhatsappLog $whatsapp)
    {
        $whatsapp->delete();
        return back()->with('success', 'WhatsApp record deleted successfully.');
    }

    public function storeFromMobile(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'sent_at' => 'nullable|date',
        ]);

        $log = CrmWhatsappLog::create([
            'phone' => $request->phone,
            'parent_name' => $request->parent_name,
            'message' => $request->message,
            'status' => $request->status ?? 'sent',
            'sent_at' => $request->sent_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp log saved successfully.',
            'data' => $log,
        ], 201);
    }
}
