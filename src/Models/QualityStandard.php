<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;

class QualityStandard extends Model
{
    protected $table = 'quality_standards';

    protected $fillable = [
        'name',
        'category',
        'description',
        'target_score',
        'target_roles',
        'is_active',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_active' => 'boolean', // Recomendado: Convierte 1/0 a true/false automáticamente
        'target_score' => 'float', // Asegura que la meta se maneje como número
    ];

    /**
     * Relación: Un estándar tiene muchas calificaciones.
     */
    public function ratings()
    {
        return $this->hasMany(StandardRating::class);
    }

    /**
     * Atributo calculado: Promedio actual de votos (Ej: 4.2)
     * Se accede como: $standard->current_score
     */
    public function getCurrentScoreAttribute()
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }
}
