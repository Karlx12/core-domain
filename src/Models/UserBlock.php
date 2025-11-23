<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $blocked_by
 * @property string $reason
 * @property string $block_type
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon $blocked_at
 * @property \Illuminate\Support\Carbon|null $blocked_until
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $unblocked_at
 * @property int|null $unblocked_by
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_currently_blocked
 * @property-read string|null $remaining_time
 * @property-read string $block_type_label
 * @property-read \Illuminate\Foundation\Auth\User|null $user
 * @property-read \Illuminate\Foundation\Auth\User|null $blockedByUser
 * @property-read \Illuminate\Foundation\Auth\User|null $unblockedByUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock currentlyBlocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock automatic()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock manual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereBlockedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereBlockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereBlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereBlockedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereUnblockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereUnblockedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBlock whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UserBlock extends Model
{
    protected $table = 'user_blocks';

    protected $fillable = [
        'user_id',
        'blocked_by',
        'reason',
        'block_type',
        'ip_address',
        'blocked_at',
        'blocked_until',
        'is_active',
        'unblocked_at',
        'unblocked_by',
        'metadata',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'blocked_until' => 'datetime',
        'unblocked_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relación con el usuario bloqueado
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'));
    }

    /**
     * Relación con el usuario que bloqueó
     */
    public function blockedByUser(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'), 'blocked_by');
    }

    /**
     * Relación con el usuario que desbloqueó
     */
    public function unblockedByUser(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'), 'unblocked_by');
    }

    /**
     * Verificar si el bloqueo está actualmente vigente
     */
    public function getIsCurrentlyBlockedAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Si no tiene fecha de fin, es permanente
        if (is_null($this->blocked_until)) {
            return true;
        }

        return now()->lt($this->blocked_until);
    }

    /**
     * Obtener tiempo restante de bloqueo en formato legible
     */
    public function getRemainingTimeAttribute(): ?string
    {
        if (! $this->is_currently_blocked) {
            return null;
        }

        if (is_null($this->blocked_until)) {
            return 'Permanente';
        }

        $diff = now()->diff($this->blocked_until);

        if ($diff->d > 0) {
            return $diff->d.' día'.($diff->d > 1 ? 's' : '').' '.$diff->h.' hora'.($diff->h > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h.' hora'.($diff->h > 1 ? 's' : '').' '.$diff->i.' minuto'.($diff->i > 1 ? 's' : '');
        } else {
            return $diff->i.' minuto'.($diff->i > 1 ? 's' : '');
        }
    }

    /**
     * Obtener el label del tipo de bloqueo
     */
    public function getBlockTypeLabelAttribute(): string
    {
        return match ($this->block_type) {
            'automatic' => 'Automático',
            'manual' => 'Manual',
            default => 'Desconocido',
        };
    }

    /**
     * Scope: Bloqueos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Bloqueos vigentes (activos y no expirados)
     */
    public function scopeCurrentlyBlocked($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('blocked_until')
                    ->orWhere('blocked_until', '>', now());
            });
    }

    /**
     * Scope: Por usuario
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Bloqueos automáticos
     */
    public function scopeAutomatic($query)
    {
        return $query->where('block_type', 'automatic');
    }

    /**
     * Scope: Bloqueos manuales
     */
    public function scopeManual($query)
    {
        return $query->where('block_type', 'manual');
    }

    /**
     * Scope: Bloqueos expirados
     */
    public function scopeExpired($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '<=', now());
    }
}
