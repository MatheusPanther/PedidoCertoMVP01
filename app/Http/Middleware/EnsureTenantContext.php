<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Models\Restaurant;
use App\Services\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;

class EnsureTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        $organization = Organization::query()->first();
        $restaurant = $organization
            ? Restaurant::query()->where('organization_id', $organization->getKey())->first()
            : null;

        app()->instance(TenantContext::class, new TenantContext($organization, $restaurant));

        return $next($request);
    }
}
