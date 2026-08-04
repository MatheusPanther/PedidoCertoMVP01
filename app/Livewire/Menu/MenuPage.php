<?php

namespace App\Livewire\Menu;

use App\Models\Product;
use App\Services\Tenancy\TenantContext;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MenuPage extends Component
{
    public bool $createModalOpen = false;

    public string $name = '';

    public string $category = 'Combos';

    public float $price = 39.90;

    public function toggleProduct(string $productId): void
    {
        $product = app(WorkspaceDataService::class)->productsQuery()->findOrFail($productId);
        $product->update(['available' => ! $product->available]);

        $status = $product->available ? 'disponivel' : 'esgotado';
        $this->dispatch('toast', message: "{$product->name} marcado como {$status}.");
    }

    public function openCreateModal(): void
    {
        $this->reset(['name']);
        $this->category = 'Combos';
        $this->price = 39.90;
        $this->createModalOpen = true;
    }

    public function createProduct(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:1'],
        ]);

        $tenant = app(TenantContext::class);

        if (! $tenant->organization || ! $tenant->restaurant) {
            $this->dispatch('toast', message: 'Organizacao e restaurante sao obrigatorios para criar produtos.');

            return;
        }

        Product::query()->create([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'branch_id' => $tenant->restaurant->branches()->value('id'),
            'category' => $this->category,
            'name' => $this->name,
            'price' => $this->price,
            'available' => true,
            'preparation_minutes' => 20,
        ]);

        $this->createModalOpen = false;
        $this->dispatch('toast', message: 'Produto criado no cardapio.');
    }

    public function render(WorkspaceDataService $data)
    {
        return view('livewire.menu.menu-page', ['products' => $data->productsQuery()->latest()->get()]);
    }
}
