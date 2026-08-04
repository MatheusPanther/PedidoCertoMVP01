<?php

namespace App\Services\Integrations;

use App\Models\IntegrationEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class N8nEventProcessor
{
    public function process(string $integrationEventId): void
    {
        DB::transaction(function () use ($integrationEventId): void {
            $event = DB::table('integration_events')
                ->where('id', $integrationEventId)
                ->lockForUpdate()
                ->first();

            if ($event === null) {
                throw (new ModelNotFoundException)->setModel(IntegrationEvent::class, [$integrationEventId]);
            }

            $eventStatus = (string) ($event->status ?? '');

            if ($eventStatus === 'processed') {
                return;
            }

            try {
                DB::table('integration_events')
                    ->where('id', $integrationEventId)
                    ->update([
                        'status' => 'processed',
                        'processed_at' => now(),
                        'error_message' => null,
                        'updated_at' => now(),
                    ]);
            } catch (Throwable $exception) {
                DB::table('integration_events')
                    ->where('id', $integrationEventId)
                    ->update([
                        'status' => 'failed',
                        'error_message' => str($exception->getMessage())->limit(500)->toString(),
                        'updated_at' => now(),
                    ]);

                throw $exception;
            }
        });
    }
}
