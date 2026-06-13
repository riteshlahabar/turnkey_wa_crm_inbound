<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCourse extends Model
{
    use HasFactory;

    protected $table = 'crm_courses';

    protected $fillable = [
        'standard_id', 'name', 'fee_amount', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function standard()
    {
        return $this->belongsTo(CrmStandard::class, 'standard_id');
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'course_id');
    }

    public function whatsappTemplates()
    {
        return $this->hasMany(CrmWhatsappTemplate::class, 'course_id');
    }
}
