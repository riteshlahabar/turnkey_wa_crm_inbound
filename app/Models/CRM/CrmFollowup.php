<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmFollowup extends Model
{
    use HasFactory;

    protected $table = 'crm_followups';

    protected $fillable = [
        'lead_id',
        'followup_type_id',
        'assigned_user_id',
        'status',
        'followup_at',
        'note',
        'next_followup_at',
        'created_by',
    ];

    protected $casts = [
        'followup_at' => 'datetime',
        'next_followup_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function type()
    {
        return $this->belongsTo(CrmFollowupType::class, 'followup_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}