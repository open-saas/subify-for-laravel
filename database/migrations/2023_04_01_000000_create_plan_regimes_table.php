<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('subify.repositories.eloquent.plan_regime.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained(config('subify.repositories.eloquent.plan.table'));
            $table->string('name')->unique()->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('periodicity')->nullable();
            $table->string('periodicity_unit')->nullable();
            $table->integer('grace')->nullable();
            $table->string('grace_unit')->nullable();
            $table->integer('trial')->nullable();
            $table->string('trial_unit')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.repositories.eloquent.plan_regime.table'));
    }
};
