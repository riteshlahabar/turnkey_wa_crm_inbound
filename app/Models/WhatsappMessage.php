<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $table = 'whatsapp_messages'; // ✅ Exact table name
    
    protected $fillable = [
        'status_id',  // ✅ Exact column name
        'message'     // ✅ Exact column name
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id'); // ✅ Exact foreign key
    }
}
