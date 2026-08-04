<?php

use App\Models\Conversation;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('organization.{organizationId}', fn (User $user, string $organizationId) => $user->getAttribute('organization_id') === $organizationId);
Broadcast::channel('restaurant.{restaurantId}', fn (User $user, string $restaurantId) => Restaurant::query()
    ->whereKey($restaurantId)
    ->where('organization_id', $user->getAttribute('organization_id'))
    ->exists());
Broadcast::channel('branch.{branchId}', fn (User $user, string $branchId) => Restaurant::query()
    ->where('organization_id', $user->getAttribute('organization_id'))
    ->whereHas('branches', fn ($query) => $query->whereKey($branchId))
    ->exists());
Broadcast::channel('conversation.{conversationId}', fn (User $user, string $conversationId) => Conversation::query()
    ->whereKey($conversationId)
    ->where('organization_id', $user->getAttribute('organization_id'))
    ->exists());
Broadcast::channel('order.{orderId}', fn (User $user, string $orderId) => Order::query()
    ->whereKey($orderId)
    ->where('organization_id', $user->getAttribute('organization_id'))
    ->exists());
Broadcast::channel('user.{userId}', fn (User $user, string $userId) => $user->getKey() === $userId);
