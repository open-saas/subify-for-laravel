<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('subify.persistence.eloquent.benefit_plan.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_id')->constrained(config('subify.persistence.eloquent.benefit.table'));
            $table->foreignId('plan_id')->constrained(config('subify.persistence.eloquent.plan.table'));
            $table->decimal('charges', 8, 2);
            $table->boolean('is_unlimited')->default(false);
            $table->timestamps();

            $table->unique(['benefit_id', 'plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.persistence.eloquent.benefit_plan.table'));
    }
};
