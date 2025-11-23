<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $license_id
 * @property int $asset_id
 * @property \Illuminate\Support\Carbon|null $assigned_date
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IncadevUns\CoreDomain\Models\License $license
 * @property-read \IncadevUns\CoreDomain\Models\TechAsset $asset
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereLicenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereAssignedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LicenseAssignment extends Model
{
    use HasFactory;

    protected $table = 'license_assignments';

    protected $fillable = [
        'license_id',
        'asset_id',
        'assigned_date',
        'status',
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
    ];

    // probablemente necesite refactorizarse
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(TechAsset::class, 'asset_id');
    }
}
