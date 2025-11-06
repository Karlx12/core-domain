<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IncadevUns\CoreDomain\Traits\CanBeAudited;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $issue_date
 * @property array<array-key, mixed>|null $extra_data_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \IncadevUns\CoreDomain\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \IncadevUns\CoreDomain\Models\Group $group
 * @property-read \Illuminate\Foundation\Auth\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereExtraDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Certificate extends Model
{
    use CanBeAudited;

    protected $fillable = [
        'uuid',
        'user_id',
        'group_id',
        'issue_date',
        'extra_data_json',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'extra_data_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'));
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
