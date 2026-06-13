<?php

namespace App\Http\Controllers\CRM\Concerns;

use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmFollowupType;
use App\Models\CRM\CrmLeadPriority;
use App\Models\CRM\CrmLeadSource;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmStandard;
use App\Models\CRM\CrmLead;
use App\Models\User;

trait CrmFormOptions
{
    protected function formOptions(): array
    {
        return [
            'standards' => CrmStandard::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'courses' => CrmCourse::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'sources' => CrmLeadSource::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'statuses' => CrmLeadStatus::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'priorities' => CrmLeadPriority::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'followupTypes' => CrmFollowupType::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),

            'users' => User::where('status', 'active')
                ->where('role', '!=', 'admin')
                ->orderBy('name')
                ->get(),
        ];
    }

   protected function newLeadNumber(): string
{
    $prefix = 'LD-' . now()->format('Ymd') . '-';

    $lastLead = CrmLead::where('lead_no', 'like', $prefix . '%')
        ->orderByDesc('id')
        ->first();

    $nextNumber = 1;

    if ($lastLead && !empty($lastLead->lead_no)) {
        $lastNumber = (int) str_replace($prefix, '', $lastLead->lead_no);
        $nextNumber = $lastNumber + 1;
    }

    return $prefix . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
}
}