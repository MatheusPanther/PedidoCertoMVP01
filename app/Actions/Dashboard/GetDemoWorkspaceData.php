<?php

namespace App\Actions\Dashboard;

use App\Services\Workspace\WorkspaceDataService;

class GetDemoWorkspaceData
{
    public function __construct(private WorkspaceDataService $workspaceData) {}

    public function execute(): array
    {
        return $this->workspaceData->overview();
    }
}
