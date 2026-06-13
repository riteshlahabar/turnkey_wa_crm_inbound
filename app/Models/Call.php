<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
    ];

    // Relationship: Call has many Followups
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }

    // Get latest followup
    public function latestFollowup()
    {
        return $this->hasOne(Followup::class)->latestOfMany();
    }

    // Get current status through latest followup
    public function currentStatus()
    {
        return $this->hasOneThrough(
            Status::class,
            Followup::class,
            'call_id',
            'id',
            'id',
            'status_id'
        )->latest('followups.created_at');
    }

    // Check if WhatsApp was sent
    public function hasWhatsAppSent()
    {
        return $this->followups()->whereNotNull('created_at')->exists();
    }

    // Get last WhatsApp sent time
    public function getLastWhatsAppSentAttribute()
    {
        return $this->followups()->latest()->first()?->created_at;
    }
}
