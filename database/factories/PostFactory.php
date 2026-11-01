<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'category' => fake()->randomElement(Post::CATEGORIES),
            'excerpt' => fake()->paragraph(),
            'cover_image' => null,
            'content' => "## Introduction\n\n".fake()->paragraph()."\n\n- First point\n- Second point",
            'status' => 'draft',
            'published_at' => null,
            'reading_minutes' => 1,
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'reading_minutes' => 3,
        ]);
    }
}
