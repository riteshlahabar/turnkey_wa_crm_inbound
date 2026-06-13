<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Models\CRM\CrmFollowupType;

class FollowupTypeController extends BaseMasterController
{
    protected string $modelClass = CrmFollowupType::class;
    protected string $viewFolder = 'followup-types';
    protected string $routePrefix = 'settings.followup-types';
    protected string $title = 'Follow-up Type';
    protected array $extraFields = [];
}
