<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $analyzable_type
 * @property int $analyzable_id
 * @property string $analysis_type
 * @property string $period
 * @property float|null $score
 * @property float|null $rate
 * @property int|null $total_events
 * @property int|null $completed_events
 * @property string|null $risk_level
 * @property array<array-key, mixed>|null $metrics
 * @property array<array-key, mixed>|null $trends
 * @property array<array-key, mixed>|null $patterns
 * @property array<array-key, mixed>|null $comparisons
 * @property array<array-key, mixed>|null $triggers
 * @property array<array-key, mixed>|null $recommendations
 * @property \Illuminate\Support\Carbon $calculated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $analyzable
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereAnalyzableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereAnalyzableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereAnalysisType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereTotalEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereCompletedEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereTrends($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic wherePatterns($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereComparisons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereTriggers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereRecommendations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAnalytic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DataAnalytic extends Model
{
    protected $fillable = [
        'analyzable_type',
        'analyzable_id',
        'analysis_type',
        'period',
        'score',
        'rate',
        'total_events',
        'completed_events',
        'risk_level',
        'metrics',
        'trends',
        'patterns',
        'comparisons',
        'triggers',
        'recommendations',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'rate' => 'decimal:2',
        'metrics' => 'array',
        'trends' => 'array',
        'patterns' => 'array',
        'comparisons' => 'array',
        'triggers' => 'array',
        'recommendations' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function analyzable(): MorphTo
    {
        return $this->morphTo();
    }
}
