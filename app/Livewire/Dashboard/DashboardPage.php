<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetDemoWorkspaceData;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DashboardPage extends Component
{
    public string $period = 'last_30_days';

    public function render(GetDemoWorkspaceData $data)
    {
        return view('livewire.dashboard.dashboard-page', ['workspace' => $data->execute()]);
    }
}
