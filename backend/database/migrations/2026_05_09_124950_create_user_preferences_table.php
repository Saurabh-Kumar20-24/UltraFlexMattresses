<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();

            // Nullable — guests have no user_id
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Guest tracking
            $table->string('session_id', 100)->nullable()->index();

            // Preference answers
            $table->string('shopping_for', 50)->nullable();
            // myself, partner, child, parents

            $table->string('sleep_concern', 50)->nullable();
            // back_pain, sleep_hot, partner_disturbance, comfort

            $table->string('budget_range', 50)->nullable();
            // under_10k, 10k_25k, 25k_50k, no_limit

            // Track if modal was shown and completed
            $table->boolean('modal_completed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
