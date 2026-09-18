<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'document',
        'phone',
        'daily_rate',
        'payment_frequency',
        'active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class)->latest();
    }

    public function getPendingEarningsAttribute(): float
    {
        return (float) $this->workLogs()->where('paid', false)->sum('amount_earned');
    }
}
