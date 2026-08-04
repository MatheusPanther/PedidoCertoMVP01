<?php

namespace App\Livewire\Remarketing;

use App\Models\Automation;
use App\Services\Workspace\WorkspaceDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RemarketingPage extends Component
{
    public function toggleAutomation(string $automationId): void
    {
        $automation = app(WorkspaceDataService::class)->automationsQuery()->findOrFail($automationId);
        $automation->update(['status' => $automation->status === 'active' ? 'paused' : 'active']);

        $this->dispatch('toast', message: "Automacao {$automation->name} atualizada.");
    }

    public function duplicateAutomation(string $automationId): void
    {
        $automation = app(WorkspaceDataService::class)->automationsQuery()->findOrFail($automationId);

        Automation::query()->create([
            'organization_id' => $automation->organization_id,
            'restaurant_id' => $automation->restaurant_id,
            'branch_id' => $automation->branch_id,
            'name' => $automation->name.' (copia)',
            'trigger' => $automation->trigger,
            'status' => 'paused',
            'trigger_config' => $automation->trigger_config,
            'actions' => $automation->actions,
            'metrics' => ['executions' => 0, 'recovered_orders' => 0, 'recovered_revenue' => 0],
        ]);

        $this->dispatch('toast', message: 'Automacao duplicada como rascunho pausado.');
    }

    public function render(WorkspaceDataService $data)
    {
        return view('livewire.remarketing.remarketing-page', ['automations' => $data->automationsQuery()->latest()->get()]);
    }
}
