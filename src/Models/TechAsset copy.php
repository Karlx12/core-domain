<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IncadevUns\CoreDomain\Enums\TechAssetStatus;
use IncadevUns\CoreDomain\Enums\TechAssetType;

/**
 * @property int $id
 * @property string $name
 * @property TechAssetType|null $type
 * @property TechAssetStatus $status
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $acquisition_date
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Foundation\Auth\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereAcquisitionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TechAsset whereUserId($value)
 *
 * @mixin \Eloquent
 */
class TechAsset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'user_id',
        'acquisition_date',
        'expiration_date',
    ];

    protected $casts = [
        'type' => TechAssetType::class,
        'status' => TechAssetStatus::class,
        'acquisition_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'));
    }

    #me faltaría agregar estas líneas en el Incadev/CoreDomain
    public function softwares(): HasMany{
        return $this->hasMany(Software::class, 'asset_id');
    }

    public function hardwares(): HasMany {
        return $this->hasMany(Hardware::class, 'asset_id');
    }
}
