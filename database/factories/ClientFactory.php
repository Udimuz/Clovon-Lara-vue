<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
//use App\Models\Client;
//namespace App\Models;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
	// protected $model = Client::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		return [
//			'first_name' => $this->faker->firstName(),
//			'last_name' => $this->faker->lastName(),
//			'email' => $this->faker->safeEmail(),

			'first_name' => fake()->firstName(),
			'last_name' => fake()->lastName(),
			'email' => fake()->safeEmail(),
		];
    }
}
