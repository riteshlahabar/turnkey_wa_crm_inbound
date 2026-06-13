<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'mobile', 'profile_image', 'password', 'role', 'status', 'monthly_target',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'monthly_target' => 'integer',
    ];

    public function leads()
    {
        return $this->hasMany(\App\Models\CRM\CrmLead::class, 'assigned_user_id');
    }

    public function createdFollowups()
    {
        return $this->hasMany(\App\Models\CRM\CrmFollowup::class, 'created_by');
    }
    public function assignedLeads()
{
    return $this->hasMany(\App\Models\CRM\CrmLead::class, 'assigned_user_id');
}

public function assignedFollowups()
{
    return $this->hasMany(\App\Models\CRM\CrmFollowup::class, 'assigned_user_id');
}
}
