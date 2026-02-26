<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'professional_id', 'leave_type', 'start_date', 'end_date', 'reason', 'status', 'approved_by'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
