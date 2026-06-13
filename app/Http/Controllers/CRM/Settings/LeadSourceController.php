<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Models\CRM\CrmLeadSource;

class LeadSourceController extends BaseMasterController
{
    protected string $modelClass = CrmLeadSource::class;
    protected string $viewFolder = 'lead-sources';
    protected string $routePrefix = 'settings.lead-sources';
    protected string $title = 'Lead Source';
    protected array $extraFields = [];
}
