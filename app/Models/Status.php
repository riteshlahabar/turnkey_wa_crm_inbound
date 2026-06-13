<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses'; // ✅ Exact table name
    
    protected $fillable = ['title']; // ✅ Exact column name

    // ✅ ADD THIS RELATIONSHIP
    public function whatsappMessage()
    {
        return $this->hasOne(WhatsappMessage::class, 'status_id', 'id');
    }
}
