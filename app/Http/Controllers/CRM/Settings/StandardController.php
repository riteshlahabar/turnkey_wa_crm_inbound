<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Models\CRM\CrmStandard;

class StandardController extends BaseMasterController
{
    protected string $modelClass = CrmStandard::class;
    protected string $viewFolder = 'standards';
    protected string $routePrefix = 'settings.standards';
    protected string $title = 'Level';
    protected array $extraFields = [];
}
