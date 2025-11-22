<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StrategicGoal
 *
 * @property int $id
 * @property string $title
 * @property string $category
 * @property array $target_roles
 * @property string|null $description
 * @property float|int $target_score
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GoalRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read float $current_average
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal query()
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereTargetRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereTargetScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StrategicGoal whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class StrategicGoal extends Model
{
    protected $table = 'strategic_goals';

    protected $fillable = [
        'title', 'category', 'target_roles',
        'description', 'target_score', 'is_active',
    ];

    // Esto convierte automáticamente el JSON de la BD a un Array de PHP
    protected $casts = [
        'target_roles' => 'array',
    ];

    public function ratings()
    {
        return $this->hasMany(GoalRating::class);
    }

    public function getCurrentAverageAttribute()
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }
}
