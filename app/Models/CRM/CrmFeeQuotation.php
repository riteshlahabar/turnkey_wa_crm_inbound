<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmFeeQuotation extends Model
{
    use HasFactory;

    protected $table = 'crm_fee_quotations';

    protected $fillable = [
        'lead_id',
        'course_id',
        'fee_amount',
        'discount_amount',
        'final_amount',
        'note',
        'created_by',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Backward-compatible accessors
    |--------------------------------------------------------------------------
    | These help if any old Blade file still uses:
    | $quotation->total_fee, $quotation->discount, $quotation->final_fee
    */

    public function getTotalFeeAttribute()
    {
        return $this->fee_amount;
    }

    public function getDiscountAttribute()
    {
        return $this->discount_amount;
    }

    public function getFinalFeeAttribute()
    {
        return $this->final_amount;
    }

    public function getInstallmentNoteAttribute()
    {
        return $this->note;
    }

    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function course()
    {
        return $this->belongsTo(CrmCourse::class, 'course_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}