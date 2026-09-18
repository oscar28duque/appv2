<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'media_id',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    /**
     * Relación con el archivo multimedia asociado
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Scope para consultar solo noticias publicadas
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }
}
