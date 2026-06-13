<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Followup extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_id',
        'status_id',
        'remark',
    ];

    // Relationship: Followup belongs to Call
    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    // Relationship: Followup belongs to Status
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
