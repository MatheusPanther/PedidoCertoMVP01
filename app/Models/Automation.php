<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $restaurant_id
 * @property string|null $branch_id
 * @property string $name
 * @property string $trigger
 * @property string $status
 * @property array<string, mixed> $trigger_config
 * @property array<int, array<string, mixed>> $actions
 * @property array<string, mixed>|null $metrics
 */
class Automation extends Model
{
    use BelongsToTenant, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'trigger_config' => 'array',
            'actions' => 'array',
            'metrics' => 'array',
        ];
    }
}
