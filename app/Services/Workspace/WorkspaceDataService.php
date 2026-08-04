<?php

namespace App\Services\Workspace;

use App\Enums\OrderStatus;
use App\Models\Automation;
use App\Models\Campaign;
use App\Models\Conversation;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DashboardMetric;
use App\Models\IntegrationEvent;
use App\Models\Order;
use App\Models\Product;
use App\Services\Tenancy\TenantContext;
use App\Support\BrazilFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WorkspaceDataService
{
    public function __construct(private TenantContext $tenant) {}

    public function overview(): array
    {
        $orders = $this->ordersQuery()->with('customer')->latest()->limit(8)->get();
        $customers = $this->customersQuery()->latest()->limit(8)->get();
        $conversations = $this->conversationsQuery()->with('customer')->latest('last_message_at')->limit(8)->get();
        $products = $this->productsQuery()->latest()->limit(8)->get();
        $grossRevenue = (float) $this->metric('gross_revenue', $orders->sum('total'));
        $ordersCount = (int) $this->metric('orders_count', $orders->count());
        $aiRevenue = (float) $this->metric('ai_revenue', $orders->where('origin', 'ai')->sum('total'));
        $remarketingRevenue = (float) $this->metric('remarketing_recovered_revenue', 0);
        $ticket = $ordersCount > 0 ? $grossRevenue / $ordersCount : 0;

        return [
            'restaurant' => $this->tenant->restaurant->name ?? 'Restaurante',
            'branch' => $this->tenant->restaurant?->branches()->value('name') ?? 'Todas as unidades',
            'period' => 'Últimos 30 dias',
            'metrics' => [
                ['label' => 'Faturamento bruto', 'value' => BrazilFormat::money($grossRevenue), 'change' => '+18,4%', 'tone' => 'success', 'href' => route('finance')],
                ['label' => 'Pedidos', 'value' => BrazilFormat::number($ordersCount), 'change' => '+11,2%', 'tone' => 'success', 'href' => route('orders')],
                ['label' => 'Ticket médio', 'value' => BrazilFormat::money($ticket), 'change' => '+6,8%', 'tone' => 'success', 'href' => route('reports')],
                ['label' => 'Clientes novos', 'value' => BrazilFormat::number($customers->count()), 'change' => '+9,1%', 'tone' => 'success', 'href' => route('customers')],
                ['label' => 'Vendas pela automação', 'value' => BrazilFormat::money($aiRevenue), 'change' => 'Origem IA', 'tone' => 'info', 'href' => route('automations')],
                ['label' => 'Receita recuperada', 'value' => BrazilFormat::money($remarketingRevenue), 'change' => 'Remarketing', 'tone' => 'warning', 'href' => route('remarketing')],
                ['label' => 'Cancelamentos', 'value' => BrazilFormat::number($this->ordersQuery()->where('status', OrderStatus::Cancelled->value)->count()), 'change' => '-3,2%', 'tone' => 'success', 'href' => route('orders')],
                ['label' => 'Tempo médio de atendimento', 'value' => '1m 48s', 'change' => 'SLA demo', 'tone' => 'success', 'href' => route('inbox')],
            ],
            'revenue' => $this->revenueSeries($grossRevenue),
            'funnel' => $this->funnel($conversations->count(), $ordersCount),
            'insights' => $this->insights($customers->count(), $remarketingRevenue),
            'conversations' => $this->conversationRows($conversations),
            'orders' => $this->orderRows($orders),
            'customers' => $this->customerRows($customers),
            'products' => $this->productRows($products),
            'coupons' => $this->couponRows($this->couponsQuery()->latest()->limit(8)->get()),
            'campaigns' => $this->campaignRows($this->campaignsQuery()->latest()->limit(8)->get()),
            'automations' => $this->automationRows($this->automationsQuery()->latest()->limit(8)->get()),
            'integration_events' => $this->integrationRows(),
        ];
    }

    /**
     * @return Builder<Order>
     */
    public function ordersQuery(): Builder
    {
        return Order::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Customer>
     */
    public function customersQuery(): Builder
    {
        return Customer::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Conversation>
     */
    public function conversationsQuery(): Builder
    {
        return Conversation::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Product>
     */
    public function productsQuery(): Builder
    {
        return Product::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Coupon>
     */
    public function couponsQuery(): Builder
    {
        return Coupon::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Campaign>
     */
    public function campaignsQuery(): Builder
    {
        return Campaign::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    /**
     * @return Builder<Automation>
     */
    public function automationsQuery(): Builder
    {
        return Automation::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id));
    }

    private function metric(string $key, float|int $fallback): float|int
    {
        return DashboardMetric::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where('organization_id', $id))
            ->when($this->tenant->restaurantId(), fn (Builder $query, string $id) => $query->where('restaurant_id', $id))
            ->where('metric_key', $key)
            ->latest('period_ended_at')
            ->value('value') ?? $fallback;
    }

    private function revenueSeries(float $grossRevenue): array
    {
        $base = max($grossRevenue / 8, 200);

        return collect(range(1, 8))->map(fn (int $index) => round($base * (0.72 + ($index * 0.06)), 2))->all();
    }

    private function funnel(int $conversations, int $orders): array
    {
        $started = max($conversations, $orders);

        return [
            ['label' => 'Conversas', 'value' => $started],
            ['label' => 'Cardápios', 'value' => max(0, (int) round($started * 0.78))],
            ['label' => 'Produtos', 'value' => max(0, (int) round($started * 0.61))],
            ['label' => 'Pedidos iniciados', 'value' => max(0, (int) round($started * 0.5))],
            ['label' => 'Confirmados', 'value' => $orders],
            ['label' => 'Finalizados', 'value' => max(0, $orders - 1)],
        ];
    }

    private function insights(int $customersCount, float $remarketingRevenue): array
    {
        return [
            ['title' => 'Reativação disponível', 'body' => "{$customersCount} clientes podem entrar em um segmento de recompra.", 'impact' => BrazilFormat::money($remarketingRevenue), 'route' => route('campaigns')],
            ['title' => 'Operação assistida pela IA', 'body' => 'Pedidos com origem IA estão sendo atribuídos ao dashboard financeiro.', 'impact' => 'Origem rastreada', 'route' => route('automations')],
            ['title' => 'Cardápio com ação rápida', 'body' => 'Produtos podem ser ativados, desativados ou marcados como esgotados sem sair da lista.', 'impact' => 'Menos atrito', 'route' => route('menu')],
        ];
    }

    /**
     * @param  Collection<int, Conversation>  $conversations
     */
    private function conversationRows(Collection $conversations): array
    {
        return $conversations->map(fn (Conversation $conversation) => [
            'id' => (string) $conversation->getKey(),
            'name' => $conversation->customer->name,
            'channel' => ucfirst($conversation->channel->value),
            'status' => str($conversation->status->value)->replace('_', ' ')->title()->toString(),
            'wait' => $conversation->last_message_at?->diffForHumans(short: true) ?? '-',
            'last' => $conversation->messages()->latest('sent_at')->value('body') ?? 'Sem mensagens recentes.',
            'ai_paused' => $conversation->ai_paused,
        ])->all();
    }

    /**
     * @param  Collection<int, Order>  $orders
     */
    private function orderRows(Collection $orders): array
    {
        return $orders->map(fn (Order $order) => [
            'id' => (string) $order->getKey(),
            'number' => '#'.$order->number,
            'customer' => $order->customer->name,
            'status' => str($order->status->value)->replace('_', ' ')->title()->toString(),
            'status_value' => $order->status->value,
            'total' => BrazilFormat::money($order->total),
            'sla' => $order->estimated_delivery_at?->diffForHumans(short: true, parts: 1) ?? '-',
        ])->all();
    }

    /**
     * @param  Collection<int, Customer>  $customers
     */
    private function customerRows(Collection $customers): array
    {
        return $customers->map(fn (Customer $customer) => [
            'id' => (string) $customer->getKey(),
            'name' => $customer->name,
            'phone' => $customer->phone,
            'segment' => collect($customer->tags ?? [])->first() ?? 'Recorrente',
            'orders' => $customer->orders_count,
            'spent' => BrazilFormat::money($customer->total_spent),
            'neighborhood' => $customer->neighborhood ?? '-',
        ])->all();
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    private function productRows(Collection $products): array
    {
        return $products->map(fn (Product $product) => [
            'id' => (string) $product->getKey(),
            'name' => $product->name,
            'category' => $product->category,
            'price' => BrazilFormat::money($product->price),
            'available' => $product->available,
            'stock' => $product->stock ?? '-',
        ])->all();
    }

    /**
     * @param  Collection<int, Coupon>  $coupons
     */
    private function couponRows(Collection $coupons): array
    {
        return $coupons->map(fn (Coupon $coupon) => [
            'id' => (string) $coupon->getKey(),
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'status' => $coupon->status,
            'uses' => $coupon->uses_count,
            'value' => $coupon->type === 'percentage' ? "{$coupon->value}%" : BrazilFormat::money($coupon->value),
        ])->all();
    }

    /**
     * @param  Collection<int, Campaign>  $campaigns
     */
    private function campaignRows(Collection $campaigns): array
    {
        return $campaigns->map(fn (Campaign $campaign) => [
            'id' => (string) $campaign->getKey(),
            'name' => $campaign->name,
            'channel' => ucfirst($campaign->channel->value),
            'status' => $campaign->status,
            'scheduled_at' => BrazilFormat::dateTime($campaign->scheduled_at),
            'recipients' => $campaign->metrics['recipients'] ?? 0,
        ])->all();
    }

    /**
     * @param  Collection<int, Automation>  $automations
     */
    private function automationRows(Collection $automations): array
    {
        return $automations->map(fn (Automation $automation) => [
            'id' => (string) $automation->getKey(),
            'name' => $automation->name,
            'trigger' => $automation->trigger,
            'status' => $automation->status,
            'executions' => $automation->metrics['executions'] ?? 0,
            'revenue' => BrazilFormat::money($automation->metrics['recovered_revenue'] ?? 0),
        ])->all();
    }

    /**
     * @return Collection<int, IntegrationEvent>
     */
    private function integrationRows(): Collection
    {
        return IntegrationEvent::query()
            ->when($this->tenant->organizationId(), fn (Builder $query, string $id) => $query->where(fn (Builder $scope) => $scope
                ->where('organization_id', $id)
                ->orWhereNull('organization_id')))
            ->latest()
            ->limit(8)
            ->get();
    }
}
