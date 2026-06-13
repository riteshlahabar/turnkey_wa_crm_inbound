<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmWhatsappTemplate extends Model
{
    use HasFactory;

    protected $table = 'crm_whatsapp_templates';

    protected $fillable = [
        'course_id',
        'title',
        'message',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(CrmCourse::class, 'course_id');
    }
}
