<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'professional_id', 'amount', 'method', 'status', 'request_id',
        'account_holder', 'account_number', 'ifsc_code', 'bank_name',
        'bank_account_id', 'upi_id', 'transaction_reference', 'admin_note',
        'rejection_reason', 'processed_at'
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function professional(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
