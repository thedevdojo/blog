<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Post>
 */
final class PostFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Post::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(),
            'body' => fake()->paragraphs(10, true),
            'image' => 'https://picsum.photos/1280/720?random=' . fake()->unique()->slug(),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->optional()->word,
            'type' => fake()->word,
            'status' => 'published',
            'active' => 1,
            'featured' => fake()->boolean,
            'meta_title' => fake()->optional()->word,
            'meta_description' => fake()->optional()->word,
            'meta_schema' => fake()->optional()->text,
            'meta_data' => fake()->optional()->text,
        ];
    }
}
