# Pedido Certo - Deploy

Stack alvo: PHP 8.3+, Laravel 13, MySQL 8.4 LTS, Redis, Laravel Reverb, Nginx e PHP-FPM.

Laravel Horizon deve ser ativado quando houver release compativel com Illuminate 13. Na validacao local atual, `laravel/horizon` ainda declara suporte somente ate Illuminate 12, entao os workers usam `queue:work redis` com filas nomeadas.

## Local

1. Copie `.env.example` para `.env`.
2. Execute `docker compose up --build`.
3. Dentro do container `app`, execute `php artisan key:generate`, `php artisan migrate --seed` e `php artisan storage:link`.
4. Acesse `http://localhost:8000/app/dashboard`.

## Workers e agenda

Use Supervisor para manter `php artisan queue:work redis` e `php artisan reverb:start` ativos. Configure cron com:

```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

## Webhook n8n

Endpoint: `POST /api/v1/webhooks/n8n/events`.

Headers obrigatorios:

- `X-Pedido-Certo-Event-Id`
- `X-Pedido-Certo-Timestamp`
- `X-Pedido-Certo-Signature`

A assinatura e `sha256=` + HMAC SHA-256 de `timestamp.event_id.raw_body` usando `N8N_WEBHOOK_SECRET`.
