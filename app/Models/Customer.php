<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $neighborhood
 * @property string $total_spent
 * @property int $orders_count
 * @property Carbon|null $last_order_at
 * @property bool $accepts_campaigns
 * @property array<int, string>|null $tags
 */
class Customer extends Model
{
    use BelongsToTenant, HasFactory, HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'accepts_campaigns' => 'boolean',
            'total_spent' => 'decimal:2',
            'last_order_at' => 'datetime',
            'tags' => 'array',
        ];
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
