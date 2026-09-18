<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'reservation_id',
        'work_date',
        'hours_or_days',
        'amount_earned',
        'paid',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours_or_days' => 'decimal:2',
        'amount_earned' => 'decimal:2',
        'paid' => 'boolean',
    ];

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
