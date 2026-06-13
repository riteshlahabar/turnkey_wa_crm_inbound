<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmWhatsappLog extends Model
{
    use HasFactory;

    protected $table = 'crm_whatsapp_logs';

    protected $fillable = [
        'phone', 'parent_name', 'message', 'status', 'sent_at', 'lead_id', 'user_id', 'course_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(CrmCourse::class, 'course_id');
    }
}
