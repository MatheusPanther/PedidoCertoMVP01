<?php

namespace App\Jobs;

use App\Services\Integrations\N8nEventProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessIntegrationEventJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $backoff = 30;

    public function __construct(public string $integrationEventId) {}

    public function handle(N8nEventProcessor $processor): void
    {
        $processor->process($this->integrationEventId);
    }
}
