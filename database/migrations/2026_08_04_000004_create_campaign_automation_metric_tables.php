<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('channel')->index();
            $table->string('status')->default('draft')->index();
            $table->json('audience_filters')->nullable();
            $table->string('template');
            $table->foreignUlid('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->json('metrics')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'restaurant_id', 'status']);
        });

        Schema::create('automations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('trigger')->index();
            $table->string('status')->default('active')->index();
            $table->json('trigger_config');
            $table->json('actions');
            $table->json('metrics')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'restaurant_id', 'status']);
        });

        Schema::create('dashboard_metrics', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('metric_key')->index();
            $table->decimal('value', 14, 2);
            $table->string('unit')->default('number');
            $table->timestamp('period_started_at')->index();
            $table->timestamp('period_ended_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'restaurant_id', 'branch_id', 'metric_key', 'period_started_at'], 'dashboard_metric_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_metrics');
        Schema::dropIfExists('automations');
        Schema::dropIfExists('campaigns');
    }
};
