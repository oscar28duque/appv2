<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'size' => 'integer',
    ];

    /**
     * URL pública del archivo en Storage
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Formato legible del tamaño del archivo (KB o MB)
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size) {
            return '0 KB';
        }

        if ($this->size >= 1048576) {
            return number_format($this->size / 1048576, 2) . ' MB';
        }

        return number_format($this->size / 1024, 1) . ' KB';
    }

    /**
     * Relación con las noticias asociadas
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
