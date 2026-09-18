<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'receipt_media_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public const CATEGORIES = [
        'transporte' => 'Transporte y Fletes',
        'mantenimiento' => 'Mantenimiento de Equipos',
        'combustible' => 'Combustible',
        'insumos' => 'Insumos / Cables / Cintas',
        'servicios' => 'Servicios y Logística',
        'otros' => 'Otros Gastos',
    ];

    public function receiptMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'receipt_media_id');
    }
}
