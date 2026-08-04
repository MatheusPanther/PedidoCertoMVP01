<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        $organization = Organization::factory();

        return [
            'organization_id' => $organization,
            'restaurant_id' => Restaurant::factory(['organization_id' => $organization]),
            'name' => 'Unidade '.fake()->city(),
            'neighborhood' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'active' => true,
        ];
    }
}
