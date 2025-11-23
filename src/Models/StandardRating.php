<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;

class StandardRating extends Model
{
    protected $table = 'standard_ratings';

    protected $fillable = [
        'quality_standard_id',
        'user_id',
        'score',
        'comment',
    ];

    /**
     * Relación: Una calificación pertenece a un Estándar de Calidad.
     */
    public function qualityStandard()
    {
        return $this->belongsTo(QualityStandard::class);
    }

    /**
     * Relación: Una calificación pertenece a un Usuario (el votante).
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'));
    }
}
