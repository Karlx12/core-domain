<?php
namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereRuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

class Software extends Model
{
    use HasFactory;
    protected $table = 'softwares';
    protected $fillable = [
        'asset_id',
        'software_name',
        'version',
        'type'];

    public function licenses(): HasMany{
        return $this->hasMany(License::class, 'software_id');
    }

    public function asset(): BelongsTo{
        return $this->belongsTo(TechAsset::class, 'asset_id');
    }



}