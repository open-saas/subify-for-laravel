<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('subify.persistence.eloquent.benefit.table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_consumable')->default(false);
            $table->boolean('is_quota')->default(false);
            $table->integer('periodicity')->nullable();
            $table->string('periodicity_unit')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.persistence.eloquent.benefit.table'));
    }
};
