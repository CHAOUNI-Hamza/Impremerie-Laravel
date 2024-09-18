<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'statut' => $this->faker->randomElement(['encour', 'expediee', 'livree']),
            'user_id' => $this->faker->numberBetween(1, 10), 
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'country' => $this->faker->country,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
