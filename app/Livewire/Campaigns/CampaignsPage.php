<?php

namespace App\Livewire\Campaigns;

use App\Enums\Channel;
use App\Models\Campaign;
use App\Services\Tenancy\TenantContext;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CampaignsPage extends Component
{
    public bool $createModalOpen = false;

    public string $name = '';

    public string $template = 'Oi {{customer_name}}, temos uma oferta especial para hoje.';

    public function openCreateModal(): void
    {
        $this->reset(['name']);
        $this->template = 'Oi {{customer_name}}, temos uma oferta especial para hoje.';
        $this->createModalOpen = true;
    }

    public function createCampaign(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'template' => ['required', 'string', 'max:500'],
        ]);

        $tenant = app(TenantContext::class);

        if (! $tenant->organization || ! $tenant->restaurant) {
            $this->dispatch('toast', message: 'Organizacao e restaurante sao obrigatorios para criar campanhas.');

            return;
        }

        Campaign::query()->create([
            'organization_id' => $tenant->organization->id,
            'restaurant_id' => $tenant->restaurant->id,
            'branch_id' => $tenant->restaurant->branches()->value('id'),
            'name' => $this->name,
            'channel' => Channel::Whatsapp,
            'status' => 'draft',
            'audience_filters' => ['accepts_campaigns' => true],
            'template' => $this->template,
            'scheduled_at' => now()->addHours(2),
            'metrics' => ['recipients' => 0, 'orders' => 0, 'revenue' => 0],
        ]);

        $this->createModalOpen = false;
        $this->dispatch('toast', message: 'Campanha criada como rascunho.');
    }

    public function schedule(string $campaignId): void
    {
        $campaign = app(WorkspaceDataService::class)->campaignsQuery()->findOrFail($campaignId);
        $campaign->update(['status' => 'scheduled', 'scheduled_at' => now()->addHour()]);

        $this->dispatch('toast', message: "Campanha {$campaign->name} agendada.");
    }

    public function pause(string $campaignId): void
    {
        $campaign = app(WorkspaceDataService::class)->campaignsQuery()->findOrFail($campaignId);
        $campaign->update(['status' => 'paused']);

        $this->dispatch('toast', message: "Campanha {$campaign->name} pausada.");
    }

    public function render(WorkspaceDataService $data)
    {
        return view('livewire.campaigns.campaigns-page', ['campaigns' => $data->campaignsQuery()->latest()->get()]);
    }
}
