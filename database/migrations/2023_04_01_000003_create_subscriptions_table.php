<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('subify.persistence.eloquent.subscription.table'), function (Blueprint $table) {
            $table->id();
            $table->string('subscriber_id');
            $table->string('subscriber_type');
            $table->timestamp('grace_ended_at')->nullable();
            $table->timestamp('trial_ended_at')->nullable();
            $table->timestamp('renewed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('plan_id')->constrained(config('subify.persistence.eloquent.plan.table'));
            $table->foreignId('plan_regime_id')->constrained(config('subify.persistence.eloquent.plan_regime.table'));

            $table->index(['subscriber_id', 'subscriber_type']);
            $table->unique(['plan_id', 'subscriber_id', 'subscriber_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.persistence.eloquent.subscription.table'));
    }
};
