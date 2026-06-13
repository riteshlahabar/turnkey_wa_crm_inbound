<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmClosedLeadStatus extends Model
{
    protected $table = 'crm_closed_lead_statuses';

    protected $fillable = [
        'name',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}