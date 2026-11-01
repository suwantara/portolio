<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $category
 * @property string|null $excerpt
 * @property string|null $cover_image
 * @property string $content
 * @property string $status
 * @property Carbon|null $published_at
 * @property int $reading_minutes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'title',
    'slug',
    'category',
    'excerpt',
    'cover_image',
    'content',
    'status',
    'published_at',
    'reading_minutes',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * The available post categories.
     *
     * @var array<int, string>
     */
    public const CATEGORIES = ['tutorial', 'tips', 'progress', 'notes'];

    /**
     * The available publication states.
     *
     * @var array<int, string>
     */
    public const STATUSES = ['draft', 'published'];

    /**
     * Limit the query to published posts.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Render the post content from Markdown to safe HTML.
     */
    public function renderedContent(): string
    {
        return self::renderMarkdown($this->content);
    }

    /**
     * Render Markdown to safe HTML.
     */
    public static function renderMarkdown(string $content): string
    {
        return Str::markdown($content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Estimate the reading time for a piece of Markdown content.
     */
    public static function estimateReadingMinutes(string $content): int
    {
        $text = trim(strip_tags(self::renderMarkdown($content)));
        $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []);

        return max(1, (int) ceil($words / 200));
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'reading_minutes' => 'integer',
        ];
    }
}
