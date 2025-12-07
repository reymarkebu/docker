<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prospect>
 */
class ProspectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'company_name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'industry' => $this->faker->word(),
            'email_address1' => $this->faker->unique()->safeEmail(),
            'email_address2' => $this->faker->optional()->safeEmail(),
            'email_address3' => $this->faker->optional()->safeEmail(),
            
        ];
    }
}
