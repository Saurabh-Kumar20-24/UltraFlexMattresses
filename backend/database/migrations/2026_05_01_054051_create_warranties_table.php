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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            // Nullable — warranty can be registered without an account
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            // Nullable — product may be deleted but warranty must stay
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();
            // Nullable — linked to order if purchased online
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->nullOnDelete();
            $table->string('warranty_number', 100)->unique();
            // Customer details snapshot
            // Stored separately because warranty may be registered
            // by someone other than the account holder (gifted product)
            $table->string('customer_name', 100);
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_phone', 15);
            $table->text('customer_address')->nullable();
            $table->string('customer_city', 100)->nullable();
            $table->string('customer_state', 100)->nullable();
            $table->string('customer_pincode', 10)->nullable();

            // Product snapshot at time of registration
            $table->string('product_name', 150);
            $table->string('product_sku', 100)->nullable();
            $table->string('variant_size', 50)->nullable();

             $table->date('purchase_date');
            $table->string('purchase_from', 100)->nullable(); 
            $table->decimal('purchase_amount', 10, 2)->nullable();
             $table->date('expiry_date');
             
            $table->unsignedInteger('warranty_years');

            $table->enum('status', [
                'active',
                'expired',
                'claimed',
                'rejected'
            ])->default('active');

            $table->text('claim_reason')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->text('admin_remarks')->nullable();

            $table->string('invoice_image', 255)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
