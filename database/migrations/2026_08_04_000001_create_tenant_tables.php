<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('plan')->default('growth');
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->json('onboarding_progress')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('cep', 10)->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('neighborhood')->nullable()->index();
            $table->string('city')->default('Aracaju');
            $table->string('state', 2)->default('SE');
            $table->json('delivery_zones')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
            $table->index(['organization_id', 'restaurant_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['organization_id']));
        Schema::dropIfExists('branches');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('organizations');
    }
};
