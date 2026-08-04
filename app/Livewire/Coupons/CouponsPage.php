<?php

namespace App\Livewire\Coupons;

use App\Models\Coupon;
use App\Services\Tenancy\TenantContext;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CouponsPage extends Component
{
    public bool $createModalOpen = false;

    public string $code = '';

    public string $name = '';

    public float $value = 10;

    public function openCreateModal(): void
    {
        $this->reset(['code', 'name']);
        $this->value = 10;
        $this->createModalOpen = true;
    }

    public function createCoupon(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:160'],
            'value' => ['required', 'numeric', 'min:1', 'max:90'],
        ]);

        $tenant = app(TenantContext::class);

        if (! $tenant->organization || ! $tenant->restaurant) {
            $this->dispatch('toast', message: 'Organizacao e restaurante sao obrigatorios para criar cupons.');

            return;
        }

        Coupon::query()->updateOrCreate([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'code' => strtoupper($this->code),
        ], [
            'branch_id' => $tenant->restaurant->branches()->value('id'),
            'name' => $this->name,
            'type' => 'percentage',
            'value' => $this->value,
            'minimum_order' => 40,
            'status' => 'active',
            'rules' => ['max_per_phone' => 1],
        ]);

        $this->createModalOpen = false;
        $this->dispatch('toast', message: 'Cupom salvo e pronto para campanhas.');
    }

    public function toggleStatus(string $couponId): void
    {
        $coupon = app(WorkspaceDataService::class)->couponsQuery()->findOrFail($couponId);
        $coupon->update(['status' => $coupon->status === 'active' ? 'paused' : 'active']);

        $this->dispatch('toast', message: "Cupom {$coupon->code} atualizado.");
    }

    public function render(WorkspaceDataService $data)
    {
        return view('livewire.coupons.coupons-page', ['coupons' => $data->couponsQuery()->latest()->get()]);
    }
}
