<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property string $value
 * @property string $minimum_order
 * @property int $uses_count
 * @property string $status
 * @property array<string, mixed>|null $rules
 */
class Coupon extends Model
{
    use BelongsToTenant, HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'minimum_order' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'rules' => 'array',
        ];
    }
}
