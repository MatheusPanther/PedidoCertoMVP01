<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $category
 * @property string $name
 * @property string $price
 * @property int|null $stock
 * @property bool $available
 */
class Product extends Model
{
    use BelongsToTenant, HasFactory, HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promotional_price' => 'decimal:2',
            'available' => 'boolean',
            'tags' => 'array',
            'availability_windows' => 'array',
        ];
    }
}
