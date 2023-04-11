<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create(config('subify.repositories.eloquent.benefit_usage.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_id')->constrained(config('subify.repositories.eloquent.benefit.table'));
            $table->decimal('amount', 8, 2);
            $table->string('subscriber_id');
            $table->string('subscriber_type');
            $table->timestamp('expired_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['subscriber_id', 'subscriber_type']);
            $table->index(['benefit_id', 'subscriber_id', 'subscriber_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subify.repositories.eloquent.benefit_usage.table'));
    }
};
