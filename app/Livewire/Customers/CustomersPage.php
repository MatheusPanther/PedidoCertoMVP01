<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Services\Tenancy\TenantContext;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CustomersPage extends Component
{
    public string $search = '';

    public bool $createModalOpen = false;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $neighborhood = '';

    public function openCreateModal(): void
    {
        $this->reset(['name', 'phone', 'email', 'neighborhood']);
        $this->createModalOpen = true;
    }

    public function createCustomer(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'neighborhood' => ['nullable', 'string', 'max:120'],
        ]);

        $tenant = app(TenantContext::class);

        if (! $tenant->organization || ! $tenant->restaurant) {
            $this->dispatch('toast', message: 'Organizacao e restaurante sao obrigatorios para criar clientes.');

            return;
        }

        Customer::query()->updateOrCreate([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'phone' => $this->phone,
        ], [
            'branch_id' => $tenant->restaurant->branches()->value('id'),
            'name' => $this->name,
            'email' => $this->email ?: null,
            'neighborhood' => $this->neighborhood ?: null,
            'tags' => ['Manual'],
        ]);

        $this->createModalOpen = false;
        $this->dispatch('toast', message: 'Cliente salvo no CRM.');
    }

    public function render(WorkspaceDataService $data)
    {
        $customers = $data->customersQuery()
            ->when($this->search !== '', fn ($query) => $query->where(fn ($scope) => $scope
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('neighborhood', 'like', "%{$this->search}%")))
            ->latest()
            ->get();

        return view('livewire.customers.customers-page', ['customers' => $customers]);
    }
}
