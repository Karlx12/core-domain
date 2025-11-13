<?php

namespace IncadevUns\CoreDomain\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereRuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LicenseAssignment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

class LicenseAssignment extends Model{

    use HasFactory;

    protected $table = 'license_assignments';

    protected $fillable = [
        'license_id',
        'asset_id',
        'assigned_date',
        'status'
    ];
    
    protected $casts = [
        'assigned_date' => 'datetime'
    ];

    #probablemente necesite refactorizarse
    public function license(): BelongsTo{
        return $this->belongsTo(License::class, 'license_id');
    }

    public function asset(): BelongsTo{
        return $this->belongsTo(TechAsset::class, 'asset_id');
    }
}