<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCall extends Model
{
    use HasFactory;

    protected $table = 'crm_calls';

    protected $fillable = [
        'phone', 'caller_name', 'call_type', 'received_at', 'lead_id', 'user_id', 'course_id', 'whatsapp_sent_at', 'status', 'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
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
