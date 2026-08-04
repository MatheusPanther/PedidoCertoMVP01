<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        $organization = Organization::factory();

        return [
            'organization_id' => $organization,
            'restaurant_id' => Restaurant::factory(['organization_id' => $organization]),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'neighborhood' => fake()->citySuffix(),
            'total_spent' => fake()->randomFloat(2, 30, 1600),
            'orders_count' => fake()->numberBetween(0, 30),
            'accepts_campaigns' => true,
        ];
    }
}
