<?php

namespace App\Policies;

use App\Models\User;

class TenantPolicy
{
    public function accessOrganization(User $user, string $organizationId): bool
    {
        return $user->getAttribute('organization_id') === $organizationId;
    }
}
