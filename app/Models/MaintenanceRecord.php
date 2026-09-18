<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',
        'cost',
        'description',
        'status',
        'maintenance_date',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_date' => 'date',
    ];

    public const TYPES = [
        'mantenimiento_preventivo' => 'Mantenimiento Preventivo',
        'mantenimiento_correctivo' => 'Mantenimiento Correctivo (Reparación)',
        'baja_inventario' => 'Baja de Inventario (Pérdida / Daño Irreparable)',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
