<?php

namespace App\Models;

use App\Enums\Channel;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $name
 * @property Channel $channel
 * @property string $status
 * @property array<string, mixed>|null $audience_filters
 * @property string $template
 * @property Carbon|null $scheduled_at
 * @property array<string, mixed>|null $metrics
 */
class Campaign extends Model
{
    use BelongsToTenant, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'channel' => Channel::class,
            'metrics' => 'array',
            'audience_filters' => 'array',
        ];
    }
}
