<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLeadStatus extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_statuses';

    protected $fillable = [
        'name', 'color', 'sort_order', 'is_default', 'is_final', 'is_admission', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_final' => 'boolean',
        'is_admission' => 'boolean',
        'is_active' => 'boolean',
    ];
}
