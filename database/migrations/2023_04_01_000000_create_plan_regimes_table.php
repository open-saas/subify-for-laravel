<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create(config('subify.repositories.eloquent.plan_regime.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained(config('subify.repositories.eloquent.plan.table'));
            $table->string('name')->unique()->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('periodicity')->nullable();
            $table->string('grace')->nullable();
            $table->string('trial')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.repositories.eloquent.plan_regime.table'));
    }
};
