<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->enum('type', [
                'company_owned',
                'dealer',
                'distributor'
            ])->default('dealer');

            $table->string('phone', 15)->nullable();
            $table->string('whatsapp', 15)->nullable();
            $table->string('email', 150)->nullable();

            $table->text('address');
            $table->string('landmark', 150)->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('pincode', 10);

             $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url', 500)->nullable();

            $table->json('business_hours')->nullable();

            $table->string('store_image', 255)->nullable();

             $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
