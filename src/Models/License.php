<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $software_id
 * @property string|null $key_code
 * @property string|null $provider
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property string|null $cost
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IncadevUns\CoreDomain\Models\Software|null $software
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \IncadevUns\CoreDomain\Models\LicenseAssignment> $assignments
 * @property-read int|null $assignments_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereSoftwareId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereKeyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class License extends Model
{
    use HasFactory;

    protected $table = 'licenses';

    protected $fillable = ['software_id',
        'key_code',
        'provider',
        'purchase_date',
        'expiration_date',
        'cost',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'expiration_date' => 'datetime',
        'cost' => 'decimal:2',
    ];

    // probablemente necesite refactorizarse
    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LicenseAssignment::class, 'license_id');
    }
}
