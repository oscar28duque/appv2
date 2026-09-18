<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'company',
        'address',
        'notes',
    ];

    /**
     * Historial de reservas del cliente
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->latest();
    }

    /**
     * Saldo pendiente total por cobrar a este cliente
     */
    public function getPendingBalanceAttribute(): float
    {
        return (float) $this->reservations()
            ->whereNotIn('status', ['cancelada'])
            ->selectRaw('SUM(total_amount - paid_amount) as pending')
            ->value('pending') ?? 0.0;
    }

    /**
     * Total histórico facturado al cliente
     */
    public function getTotalBilledAttribute(): float
    {
        return (float) $this->reservations()
            ->whereNotIn('status', ['cancelada'])
            ->sum('total_amount');
    }
}
