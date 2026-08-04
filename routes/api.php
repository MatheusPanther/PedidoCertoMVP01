<?php

use App\Http\Controllers\Api\V1\DemoResourceController;
use App\Http\Controllers\Api\V1\N8nWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    Route::post('/webhooks/n8n/events', N8nWebhookController::class)
        ->middleware('n8n.hmac')
        ->name('api.v1.webhooks.n8n.events');

    foreach (['messages', 'conversations', 'customers', 'orders', 'products', 'coupons', 'automations', 'integrations'] as $resource) {
        Route::apiResource($resource, DemoResourceController::class)->only(['index', 'show']);
    }
});
