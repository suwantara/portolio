<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property array<string, mixed> $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['content'])]
class PortfolioSetting extends Model
{
    /**
     * Get the current portfolio content, falling back to the defaults.
     *
     * @return array<string, mixed>
     */
    public static function content(): array
    {
        /** @var array<string, mixed> $defaults */
        $defaults = config('portfolio.defaults', []);

        $setting = static::query()->first();

        if (! $setting instanceof self) {
            return $defaults;
        }

        $content = array_replace($defaults, $setting->content);

        foreach ($defaults as $key => $value) {
            if (
                is_array($value)
                && ! array_is_list($value)
                && is_array($content[$key] ?? null)
            ) {
                $content[$key] = array_replace($value, $content[$key]);
            }
        }

        return $content;
    }

    /**
     * Persist the portfolio content.
     *
     * @param  array<string, mixed>  $content
     */
    public static function updateContent(array $content): void
    {
        static::query()->updateOrCreate(['id' => 1], ['content' => $content]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}
