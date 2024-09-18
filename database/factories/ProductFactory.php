<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'price' => $this->faker->randomDigit,
            'description' => $this->faker->paragraph, 
            'json' => json_encode(["sffds"]),
            'qt' => json_encode(["sffds"]),
            'slug' => Str::slug($this->faker->sentence),
            'category_id' => function () {
                return \App\Models\Category::factory()->create()->id;
            },
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            }
        ];
    }
}
