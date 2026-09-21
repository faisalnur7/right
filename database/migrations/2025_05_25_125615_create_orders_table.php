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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_tracking_number')->nullable();
            
            $table->json('billing_address');
            $table->json('shipping_address')->nullable();

            $table->foreignId('payment_option_id')->nullable()->constrained('payment_options')->onDelete('set null');
            $table->string('payment_account_number');

            $table->string('transaction_id')->nullable();
            $table->string('sender_phone_number', 20)->nullable();

            $table->string('status')->default(1);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->datetime('start_processing_at')->nullable();
            $table->datetime('packaged_at')->nullable();
            $table->datetime('shipped_at')->nullable();
            $table->datetime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
