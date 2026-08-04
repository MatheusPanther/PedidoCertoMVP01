<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DashboardMetric extends Model
{
    use BelongsToTenant, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'period_started_at' => 'datetime',
            'period_ended_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
