<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLeadPriority extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_priorities';

    protected $fillable = ['name', 'color', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
