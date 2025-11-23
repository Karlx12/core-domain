<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $asset_id
 * @property string $software_name
 * @property string|null $version
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \IncadevUns\CoreDomain\Models\License> $licenses
 * @property-read int|null $licenses_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereSoftwareName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Software whereCreatedAt($value)
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

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'software_id');
    }
}
