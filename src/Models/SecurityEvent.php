<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IncadevUns\CoreDomain\Enums\SecurityEventSeverity;
use IncadevUns\CoreDomain\Enums\SecurityEventType;

/**
 * @property int $id
 * @property int|null $user_id
 * @property SecurityEventType $event_type
 * @property SecurityEventSeverity $severity
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Foundation\Auth\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent ofType(SecurityEventType $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent withSeverity(SecurityEventSeverity $severity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent critical()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent recent(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent fromIp(string $ip)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SecurityEvent extends Model
{
    protected $table = 'security_events';

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'event_type' => SecurityEventType::class,
        'severity' => SecurityEventSeverity::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con usuario
     */
    public function user(): BelongsTo
    {
        $userModelClass = config('auth.providers.users.model', 'App\Models\User');

        return $this->belongsTo($userModelClass);
    }

    /**
     * Scope: Filtrar por tipo de evento
     */
    public function scopeOfType($query, SecurityEventType $type)
    {
        return $query->where('event_type', $type->value);
    }

    /**
     * Scope: Filtrar por severidad
     */
    public function scopeWithSeverity($query, SecurityEventSeverity $severity)
    {
        return $query->where('severity', $severity->value);
    }

    /**
     * Scope: Eventos críticos
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', SecurityEventSeverity::CRITICAL->value);
    }

    /**
     * Scope: Eventos recientes
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Por usuario
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Por IP
     */
    public function scopeFromIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
