<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Organization;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_records_are_scoped_by_organization(): void
    {
        $firstOrganization = Organization::factory()->create(['slug' => 'org-a']);
        $secondOrganization = Organization::factory()->create(['slug' => 'org-b']);

        $firstRestaurant = Restaurant::factory()->create(['organization_id' => $firstOrganization->id, 'name' => 'Rest A']);
        $secondRestaurant = Restaurant::factory()->create(['organization_id' => $secondOrganization->id, 'name' => 'Rest B']);

        Customer::query()->create([
            'organization_id' => $firstOrganization->id,
            'restaurant_id' => $firstRestaurant->id,
            'name' => 'Cliente A',
            'phone' => '111',
        ]);

        Customer::query()->create([
            'organization_id' => $secondOrganization->id,
            'restaurant_id' => $secondRestaurant->id,
            'name' => 'Cliente B',
            'phone' => '222',
        ]);

        $this->assertSame(['111'], Customer::query()->forOrganization($firstOrganization->id)->pluck('phone')->all());
    }
}
