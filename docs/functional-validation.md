# Validacao funcional - Pedido Certo CRM

Data: 2026-08-04

## Ambiente local

| Item | Status | Observacao |
| --- | --- | --- |
| `.env` | OK | Criado e com `APP_KEY` configurada. |
| Banco local | OK parcial | Ambiente validado com SQLite por indisponibilidade local de MySQL/Docker/Homebrew. `.env.example` permanece preparado para MySQL. |
| Cache | OK | `CACHE_STORE=database`; `php artisan optimize:clear` executado. |
| Sessions | OK | `SESSION_DRIVER=database`; login validado com cookie. |
| Queues | OK | `QUEUE_CONNECTION=database`; fila `webhooks` processada com `queue:work database --queue=webhooks --once`. |
| Redis | Pendente externo | Configurado em `.env.example`, mas nao validado localmente por ausencia de servico Redis. |

## Dados de demonstracao

| Dado | Quantidade validada |
| --- | ---: |
| Organizacao | 1 |
| Restaurante | 1 |
| Unidade | 1 |
| Usuario demo | 1 |
| Clientes | 1 |
| Conversas | 1 |
| Mensagens | 1 |
| Produtos | 1 |
| Pedidos | 1 |
| Cupons | 1 |
| Campanhas | 1 |
| Automacoes | 1 |
| Metricas dashboard | 4 |
| Eventos integracao | 1+ |

## Matriz de paginas

| Pagina | Rota | HTTP | Funcionalidade validada |
| --- | --- | ---: | --- |
| Login | `/login` | 200 | Formulario, CSRF, autenticacao demo e redirect. |
| Dashboard | `/app/dashboard` | 200 | Metricas, grafico, funil, widgets e insights com links reais. |
| Atendimento | `/app/atendimento` | 200 | Lista, busca, filtros, chat, perfil, handoff humano/IA, mensagem outbound e criacao de pedido. |
| Pedidos | `/app/pedidos` | 200 | Kanban, lista, drawer, criacao manual e mudanca de status. |
| Clientes | `/app/clientes` | 200 | Busca, tabela, modal e upsert de cliente. |
| Cardapio | `/app/cardapio` | 200 | Lista, preview mobile, modal de produto e toggle de disponibilidade. |
| Pipeline | `/app/pipeline` | 200 | Hub operacional com atalhos para atendimento, pedidos e campanhas. |
| Cupons | `/app/cupons` | 200 | Lista real, criacao/upsert e ativacao/pausa. |
| Campanhas | `/app/campanhas` | 200 | Indicadores reais, criacao, agendamento e pausa. |
| Remarketing | `/app/remarketing` | 200 | Automacoes reais, ativacao/pausa e duplicacao. |
| Fidelidade | `/app/fidelidade` | 200 | Hub com atalhos para clientes, cupons e campanhas. |
| Relatorios | `/app/relatorios` | 200 | Hub com atalhos para financeiro, pedidos e atendimento. |
| Financeiro | `/app/financeiro` | 200 | Hub com atalhos para pedidos, campanhas e cupons. |
| Automacoes | `/app/automacoes` | 200 | Hub com atalhos para remarketing, integracoes e campanhas. |
| Integracoes n8n | `/app/integracoes` | 200 | Eventos recentes, criacao de evento teste e reprocessamento. |
| Configuracoes | `/app/configuracoes` | 200 | Hub com atalhos para integracoes, cardapio e dashboard. |

## n8n

| Requisito | Status | Evidencia |
| --- | --- | --- |
| Assinatura HMAC | OK | Payload assinado aceito com HTTP 202. |
| Idempotencia `event_id` | OK | Segundo envio retornou `duplicate_ignored`. |
| Registro de eventos | OK | Evento salvo em `integration_events`. |
| Processamento em fila | OK | Job processado na fila `webhooks`. |
| Logs de erro | OK | `failed_jobs` validado com 0 registros apos processamento. |
| Reprocessamento | OK | Tela de integracoes possui POST de reprocessamento para eventos. |

## Comandos executados

```bash
./php composer.phar install
PATH="$PWD/node-v20.20.2-darwin-x64/bin:$PATH" npm install
./php artisan optimize:clear
./php artisan migrate --force
./php artisan test
./php vendor/bin/pint --test
./php vendor/bin/phpstan analyse app/Models app/Http app/Livewire app/Services app/Jobs app/Actions app/Events app/Policies routes --level=5 --memory-limit=1G --no-progress --debug -vvv
PATH="$PWD/node-v20.20.2-darwin-x64/bin:$PATH" npm run build
./php artisan queue:work database --queue=webhooks --once
```

## Pendencias externas

| Item | Motivo |
| --- | --- |
| MySQL local | Nao havia servico MySQL disponivel no ambiente; validacao local usou SQLite. |
| Redis local | Nao havia servico Redis disponivel; filas/cache/sessoes foram validados com database drivers. |
| WhatsApp/Instagram reais | Dependem de credenciais Meta/WhatsApp Cloud API e webhooks externos. |
| n8n externo | Endpoint local esta funcional; execucao externa depende da URL publica e secret compartilhado. |
| Exportacoes/storage externo | Hub informa pendencia ate configurar disco/credenciais de storage. |
| Validacao visual em navegador real | HTTP/HTML foi validado; Playwright/browser nao estava disponivel de forma confiavel neste ambiente. |
