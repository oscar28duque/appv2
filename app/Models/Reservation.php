<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'client_id',
        'event_name',
        'event_location',
        'start_date',
        'end_date',
        'pricing_type',
        'subtotal',
        'discount',
        'total_amount',
        'paid_amount',
        'status',
        'evidence_media_id',
        'return_date_real',
        'return_status',
        'return_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'return_date_real' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public const STATUSES = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'en_curso' => 'En Curso',
        'devuelta' => 'Devuelta / Finalizada',
        'cancelada' => 'Cancelada',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function evidenceMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'evidence_media_id');
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    public function getPendingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Alerta de devolución atrasada (Requerimiento 3.1 & 3.6)
     */
    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['devuelta', 'cancelada'])) {
            return false;
        }

        return $this->end_date->isPast() && !$this->end_date->isToday();
    }

    /**
     * Duración en días
     */
    public function getDaysCountAttribute(): int
    {
        return max(1, $this->start_date->diffInDays($this->end_date) + 1);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmada', 'en_curso']);
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['cancelada'])
            ->whereRaw('total_amount > paid_amount');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmada', 'en_curso'])
            ->whereDate('end_date', '<', now()->toDateString());
    }
}
