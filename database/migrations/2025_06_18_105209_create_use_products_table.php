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
        Schema::create('use_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->nullable()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->nullable()->onDelete('cascade');
            $table->foreignId('sale_log_id')->constrained()->nullable()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('status')->default(0);
            $table->string('payment_status')->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('transaction_mobile_number')->nullable(); 
            $table->timestamp('used_at')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('use_products');
    }
};
