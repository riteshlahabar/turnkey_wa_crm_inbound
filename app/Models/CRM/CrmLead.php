<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model
{
    use HasFactory;

    protected $table = 'crm_leads';

    protected $fillable = [
        'lead_no',
        'parent_name',
        'student_name',
        'phone',
        'mobile',
        'alternate_mobile',
        'standard_id',
        'course_id',
        'school_name',
        'board',
        'address',
        'area',
        'lead_source_id',
        'lead_status_id',
        'lead_priority_id',
        'assigned_user_id',
        'created_by',
        'inquiry_date',
        'next_followup_at',
        'note',
        'admission_done_at',
        'last_activity_at',
        'is_closed',
'closed_at',
'closed_by',
'closed_note',
'closed_status_id',
    ];

    protected $casts = [
        'inquiry_date' => 'date',
        'next_followup_at' => 'datetime',
        'admission_done_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_closed' => 'boolean',
'closed_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(CrmLeadSource::class, 'lead_source_id');
    }

    public function status()
    {
        return $this->belongsTo(CrmLeadStatus::class, 'lead_status_id');
    }

    public function priority()
    {
        return $this->belongsTo(CrmLeadPriority::class, 'lead_priority_id');
    }

    public function standard()
    {
        return $this->belongsTo(CrmStandard::class, 'standard_id');
    }

    public function course()
    {
        return $this->belongsTo(CrmCourse::class, 'course_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followups()
    {
        return $this->hasMany(CrmFollowup::class, 'lead_id');
    }

    public function latestFollowup()
    {
        return $this->hasOne(CrmFollowup::class, 'lead_id')->latestOfMany();
    }

    public function calls()
    {
        return $this->hasMany(CrmCall::class, 'lead_id');
    }

    public function whatsappLogs()
    {
        return $this->hasMany(CrmWhatsappLog::class, 'lead_id');
    }

    public function quotations()
    {
        return $this->hasMany(CrmFeeQuotation::class, 'lead_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->latest();
    }

    public function scopeForLoggedInUser($query)
    {
        $user = auth()->user();

        if ($user && ($user->role ?? 'admin') !== 'admin') {
            $query->where('assigned_user_id', $user->id);
        }

        return $query;
    }
    
    public function closedBy()
{
    return $this->belongsTo(\App\Models\User::class, 'closed_by');
}

public function closedStatus()
{
    return $this->belongsTo(\App\Models\CRM\CrmClosedLeadStatus::class, 'closed_status_id');
}
}