<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $content_type
 * @property string|null $title
 * @property string|null $slug
 * @property string $content
 * @property string|null $summary
 * @property string|null $image_url
 * @property string $status
 * @property int $views
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $published_date
 * @property string|null $category
 * @property string|null $item_type
 * @property string|null $target_page
 * @property string|null $link_url
 * @property string|null $link_text
 * @property string|null $button_text
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem wherePublishedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereTargetPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereLinkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereLinkText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ContentItem extends Model
{
    protected $fillable = [
        'content_type',
        'title',
        'slug',
        'content',
        'summary',
        'image_url',
        'status',
        'views',
        'priority',
        'start_date',
        'end_date',
        'published_date',
        'category',
        'item_type',
        'target_page',
        'link_url',
        'link_text',
        'button_text',
        'seo_title',
        'seo_description',
        'metadata',
    ];

    protected $casts = [
        'views' => 'integer',
        'priority' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_date' => 'datetime',
        'metadata' => 'array',
    ];
}
