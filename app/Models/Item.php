<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'total_quantity',
        'daily_rate',
        'weekend_rate',
        'media_id',
        'status',
    ];

    protected $casts = [
        'total_quantity' => 'integer',
        'daily_rate' => 'decimal:2',
        'weekend_rate' => 'decimal:2',
    ];

    /**
     * Categorías estándar del inventario de eventos
     */
    public const CATEGORIES = [
        'Carpas y Toldos',
        'Mobiliario',
        'Mantelería y Decoración',
        'Audio',
        'Iluminación',
        'Video',
        'Estructuras',
        'Herramientas y Maquinaria',
        'Tecnología',
        'Camping y Viajes',
        'Efectos',
    ];

    /**
     * Imagen asociada mediante la biblioteca multimedia segura
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Historial de reservas donde se ha incluido este equipo
     */
    public function reservationItems(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    /**
     * Historial de mantenimientos técnicos y bajas
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    /**
     * Calcula la cantidad reservada/comprometida para un rango de fechas.
     * Considera solo reservas en estado 'confirmada' o 'en_curso'.
     */
    public function getBookedQuantityForRange(string $startDate, string $endDate, ?int $excludeReservationId = null): int
    {
        $query = DB::table('reservation_items')
            ->join('reservations', 'reservation_items.reservation_id', '=', 'reservations.id')
            ->where('reservation_items.item_id', $this->id)
            ->whereIn('reservations.status', ['confirmada', 'en_curso', 'pendiente'])
            ->whereDate('reservations.start_date', '<=', $endDate)
            ->whereDate('reservations.end_date', '>=', $startDate);

        if ($excludeReservationId) {
            $query->where('reservations.id', '!=', $excludeReservationId);
        }

        return (int) $query->sum('reservation_items.quantity');
    }

    /**
     * Obtiene el stock disponible real evitando sobrecupo
     */
    public function getAvailableQuantityForRange(string $startDate, string $endDate, ?int $excludeReservationId = null): int
    {
        if ($this->status !== 'disponible') {
            return 0;
        }

        $booked = $this->getBookedQuantityForRange($startDate, $endDate, $excludeReservationId);
        $available = $this->total_quantity - $booked;

        return max(0, $available);
    }

    /**
     * Disponibilidad para el día de hoy (KPI de requerimiento 3.1)
     */
    public function getAvailableTodayAttribute(): int
    {
        $today = now()->toDateString();
        return $this->getAvailableQuantityForRange($today, $today);
    }
}
