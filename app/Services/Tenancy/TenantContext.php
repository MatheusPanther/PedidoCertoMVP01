<?php

namespace App\Services\Tenancy;

use App\Models\Organization;
use App\Models\Restaurant;

class TenantContext
{
    public function __construct(
        public ?Organization $organization = null,
        public ?Restaurant $restaurant = null,
        public ?string $branchId = null,
    ) {}

    public function organizationId(): ?string
    {
        return $this->organization ? (string) $this->organization->getKey() : null;
    }

    public function restaurantId(): ?string
    {
        return $this->restaurant ? (string) $this->restaurant->getKey() : null;
    }
}
