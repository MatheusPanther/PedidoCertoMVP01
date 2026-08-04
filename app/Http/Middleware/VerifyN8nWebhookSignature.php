<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyN8nWebhookSignature
{
    public function handle(Request $request, Closure $next): mixed
    {
        $secret = (string) config('services.n8n.webhook_secret', 'pedido-certo-demo-secret');
        $timestamp = (string) $request->header('X-Pedido-Certo-Timestamp');
        $signature = (string) $request->header('X-Pedido-Certo-Signature');
        $eventId = (string) $request->header('X-Pedido-Certo-Event-Id');

        if ($timestamp === '' || $signature === '' || $eventId === '') {
            abort(401, 'Headers de assinatura ausentes.');
        }

        $requestTimestamp = (int) $timestamp;

        if (abs(time() - $requestTimestamp) > 300) {
            abort(401, 'Timestamp fora da janela permitida.');
        }

        $payload = $timestamp.'.'.$eventId.'.'.$request->getContent();
        $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            abort(401, 'Assinatura invalida.');
        }

        return $next($request);
    }
}
