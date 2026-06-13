<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Models\CRM\CrmLeadPriority;

class LeadPriorityController extends BaseMasterController
{
    protected string $modelClass = CrmLeadPriority::class;
    protected string $viewFolder = 'lead-priorities';
    protected string $routePrefix = 'settings.lead-priorities';
    protected string $title = 'Lead Priority';
    protected array $extraFields = [
        'color' => ['rule' => 'nullable|string|max:50'],
    ];
}
