<?php

namespace Database\Seeders;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Automation;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Conversation;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DashboardMetric;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->create([
            'name' => 'Ampliize Demo',
            'slug' => 'ampliize-demo',
        ]);

        $restaurant = Restaurant::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Pedido Certo Burger',
            'phone' => '(79) 99988-1100',
            'onboarding_progress' => ['completed_steps' => 11, 'total_steps' => 15],
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'name' => 'Unidade Atalaia',
            'neighborhood' => 'Atalaia',
            'delivery_zones' => [['name' => 'Atalaia', 'fee' => 8.00]],
        ]);

        User::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Matheus Admin',
            'email' => 'admin@pedidocerto.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $customer = Customer::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'name' => 'Marina Alves',
            'phone' => '(79) 99988-1100',
            'email' => 'marina@example.com',
            'neighborhood' => 'Atalaia',
            'total_spent' => 1482.30,
            'orders_count' => 18,
            'last_order_at' => now()->subDays(2),
            'tags' => ['VIP', 'WhatsApp'],
        ]);

        $conversation = Conversation::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'channel' => Channel::Whatsapp,
            'status' => ConversationStatus::OrderStarted,
            'last_message_at' => now(),
        ]);

        Message::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'conversation_id' => $conversation->id,
            'customer_id' => $customer->id,
            'channel' => Channel::Whatsapp,
            'direction' => 'inbound',
            'body' => 'Quero o combo smash com aneis de cebola.',
            'sent_at' => now(),
        ]);

        $order = Order::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'conversation_id' => $conversation->id,
            'number' => '1842',
            'channel' => Channel::Whatsapp,
            'status' => OrderStatus::Preparing,
            'payment_status' => PaymentStatus::Paid,
            'payment_method' => 'Pix',
            'subtotal' => 78.90,
            'delivery_fee' => 8.00,
            'discount' => 0,
            'total' => 86.90,
            'address' => 'Rua Deputado Clovis Rollemberg, 120, Atalaia, Aracaju-SE',
            'estimated_delivery_at' => now()->addMinutes(32),
            'history' => [['status' => 'preparing', 'at' => now()->toISOString()]],
        ]);

        OrderItem::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'order_id' => $order->id,
            'product_name' => 'Combo Smash Duplo',
            'quantity' => 2,
            'unit_price' => 39.45,
            'total' => 78.90,
            'addons' => ['Aneis de cebola'],
        ]);

        Product::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'category' => 'Combos',
            'name' => 'Combo Smash Duplo',
            'price' => 42.90,
            'available' => true,
        ]);

        Coupon::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'code' => 'VOLTA10',
            'name' => 'Reativacao 30 dias',
            'type' => 'percentage',
            'value' => 10,
            'minimum_order' => 40,
            'rules' => ['max_per_phone' => 1, 'cooldown_days' => 30],
        ]);

        Campaign::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'name' => 'Reativacao Atalaia 30 dias',
            'channel' => Channel::Whatsapp,
            'status' => 'scheduled',
            'audience_filters' => ['inactive_days' => 30, 'neighborhood' => 'Atalaia'],
            'template' => 'Oi {{customer_name}}, sentimos sua falta. Use VOLTA10 hoje.',
            'scheduled_at' => now()->addDay(),
            'metrics' => ['recipients' => 94, 'estimated_revenue' => 4200.00],
        ]);

        Automation::query()->create([
            'organization_id' => $organization->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
            'name' => 'Carrinho abandonado',
            'trigger' => 'cart.abandoned',
            'status' => 'active',
            'trigger_config' => ['delay_minutes' => 20, 'quiet_hours' => ['22:00', '09:00']],
            'actions' => [
                ['type' => 'send_message', 'channel' => 'whatsapp'],
                ['type' => 'create_coupon', 'code' => 'VOLTA10'],
                ['type' => 'send_webhook', 'target' => 'n8n'],
            ],
            'metrics' => ['executions' => 186, 'recovered_orders' => 24, 'recovered_revenue' => 12460.80],
        ]);

        foreach ([
            'gross_revenue' => [84320.40, 'BRL'],
            'orders_count' => [1248, 'number'],
            'ai_revenue' => [49870.00, 'BRL'],
            'remarketing_recovered_revenue' => [12460.80, 'BRL'],
        ] as $key => [$value, $unit]) {
            DashboardMetric::query()->create([
                'organization_id' => $organization->id,
                'restaurant_id' => $restaurant->id,
                'branch_id' => $branch->id,
                'metric_key' => $key,
                'value' => $value,
                'unit' => $unit,
                'period_started_at' => now()->subDays(30)->startOfDay(),
                'period_ended_at' => now()->endOfDay(),
            ]);
        }
    }
}
