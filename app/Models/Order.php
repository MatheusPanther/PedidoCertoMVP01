<?php

namespace App\Models;

use App\Enums\Channel;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $customer_id
 * @property string|null $conversation_id
 * @property string $number
 * @property Channel $channel
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 * @property string|null $payment_method
 * @property string $subtotal
 * @property string $delivery_fee
 * @property string $discount
 * @property string $total
 * @property string $origin
 * @property string|null $address
 * @property Carbon|null $estimated_delivery_at
 * @property array<int, array<string, string>>|null $history
 * @property Customer|null $customer
 * @property Collection<int, OrderItem> $items
 */
class Order extends Model
{
    use BelongsToTenant, HasFactory, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'channel' => Channel::class,
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'estimated_delivery_at' => 'datetime',
            'history' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
