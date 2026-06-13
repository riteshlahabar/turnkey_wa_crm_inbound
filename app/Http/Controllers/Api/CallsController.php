<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Status;
use App\Models\Followup;
use Illuminate\Http\Request;

class CallsController extends Controller
{
    /**
     * Get All Calls
     * GET /api/v1/calls
     */
    public function index(Request $request)
{
    $query = Call::with(['latestFollowup.status', 'followups']);

    // Filter by status
    if ($request->has('status')) {
        $query->whereHas('latestFollowup', function ($q) use ($request) {
            $q->whereHas('status', function ($sq) use ($request) {
                $sq->where('title', $request->status);
            });
        });
    }

    // Search
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('phone', 'like', "%$search%")
              ->orWhere('name', 'like', "%$search%");
        });
    }

    $calls = $query->latest('created_at')->paginate(20);

    return response()->json([
        'success' => true,
        'data' => $calls->map(function ($call) {
            // ✅ FIX: Check if any followup has WhatsApp sent
            $whatsappSent = $call->followups->contains(function ($followup) {
                return stripos($followup->remark, 'whatsapp') !== false;
            });

            return [
                'id' => $call->id,
                'phone' => $call->phone,
                'name' => $call->name,
                'time' => $call->created_at->diffForHumans(),
                'created_at' => $call->created_at->format('Y-m-d H:i:s'),
                
                // ✅ FIX: Add latestFollowup data for Flutter
                'latestFollowup' => $call->latestFollowup ? [
                    'id' => $call->latestFollowup->id,
                    'status' => [
                        'id' => $call->latestFollowup->status_id,
                        'title' => $call->latestFollowup->status->title ?? 'Select Course',
                    ],
                    'remark' => $call->latestFollowup->remark,
                ] : null,
                
                // Keep old format for backward compatibility
                'status' => $call->latestFollowup ? [
                    'id' => $call->latestFollowup->status_id,
                    'title' => $call->latestFollowup->status->title ?? 'Select Course',
                ] : null,
                
                // ✅ FIX: Return all followups with remarks
                'followups' => $call->followups->map(function ($followup) {
                    return [
                        'id' => $followup->id,
                        'status_id' => $followup->status_id,
                        'remark' => $followup->remark,
                        'created_at' => $followup->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                
                'whatsapp_sent' => $whatsappSent, // ✅ FIXED
                'followup_count' => $call->followups->count(),
            ];
        }),
        'meta' => [
            'current_page' => $calls->currentPage(),
            'last_page' => $calls->lastPage(),
            'per_page' => $calls->perPage(),
            'total' => $calls->total(),
        ],
    ]);
}


    /**
     * Get Single Call
     * GET /api/v1/calls/{id}
     */
    public function show($id)
    {
        $call = Call::with(['followups.status'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $call->id,
                'phone' => $call->phone,
                'name' => $call->name,
                'created_at' => $call->created_at->format('Y-m-d H:i:s'),
                'followups' => $call->followups->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'status' => $f->status->title ?? 'N/A',
                        'remark' => $f->remark,
                        'created_at' => $f->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create New Call
     * POST /api/v1/calls
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $call = Call::create([
            'phone' => $request->phone,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call created successfully',
            'data' => $call,
        ], 201);
    }

    /**
     * Update Call Status
     * PATCH /api/v1/calls/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'remark' => 'nullable|string',
        ]);

        $call = Call::findOrFail($id);

        Followup::create([
            'call_id' => $call->id,
            'status_id' => $request->status_id,
            'remark' => $request->remark,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
    }

    /**
     * Delete Call
     * DELETE /api/v1/calls/{id}
     */
    public function destroy($id)
    {
        $call = Call::findOrFail($id);
        $call->delete();

        return response()->json([
            'success' => true,
            'message' => 'Call deleted successfully',
        ]);
    }

    /**
     * Get All Statuses
     * GET /api/v1/statuses
     */
      // ✅ GET STATUSES WITH MESSAGES
public function getStatuses()
{
    $statuses = \App\Models\Status::with('whatsappMessage')
        ->get()
        ->map(function($status) {
            return [
                'id' => $status->id,                    
                'title' => $status->title,              
                'message' => $status->whatsappMessage?->message ?? null, 
                'created_at' => $status->created_at,    
                'updated_at' => $status->updated_at,    
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $statuses
    ]);
}

}
