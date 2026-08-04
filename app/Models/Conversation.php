<?php

namespace App\Models;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Models\Concerns\BelongsToTenant;
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
 * @property string|null $assigned_user_id
 * @property Channel $channel
 * @property ConversationStatus $status
 * @property bool $ai_paused
 * @property int $unread_count
 * @property Carbon|null $last_message_at
 * @property array<int, string>|null $tags
 * @property Customer|null $customer
 */
class Conversation extends Model
{
    use BelongsToTenant, HasFactory, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'channel' => Channel::class,
            'status' => ConversationStatus::class,
            'ai_paused' => 'boolean',
            'last_message_at' => 'datetime',
            'tags' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
