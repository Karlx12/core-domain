<?php
namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereRuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

class License extends Model{

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

    #probablemente necesite refactorizarse
    public function software(): BelongsTo{
        return $this->belongsTo(Software::class);
    }
}