<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommandeDetails>
 */
class CommandeDetailsFactory extends Factory
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
            'description' => $this->faker->paragraph,
            'quantity' => $this->faker->numberBetween(1, 10),
            'product_id' => \App\Models\Product::factory()->create()->id,
            'commande_id' => \App\Models\Commande::factory()->create()->id,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'priceUni' => $this->faker->randomFloat(2, 10, 100),
            'details' => $this->faker->text,
        ];
    }
}
