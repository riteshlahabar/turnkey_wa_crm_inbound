<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmStandard extends Model
{
    use HasFactory;

    protected $table = 'crm_standards';

    protected $fillable = ['name', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'standard_id');
    }
}
