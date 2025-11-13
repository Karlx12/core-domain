<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Databas\Eloquent\Relations\BelongsTo;

/**
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereRuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hardware whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

class Hardware extends Model
{
    use HasFactory;

    protected $table = 'hardwares';

    protected $fillable = [
        'asset_id',
        'model',
        'serial_number',
        'warranty_expiration',
        'specs'
    ];

    public function asset(): BelongsTo{
        return $this->belongsTo(TechAsset::class, 'asset_id');
    }

}