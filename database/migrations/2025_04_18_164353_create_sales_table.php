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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // Refers to users table
            $table->foreignId('admin_user_id')->constrained('admins')->onDelete('cascade');  // Who created the sale
            $table->string('invoice_no')->unique();
            $table->date('sale_date');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('status')->default(1);     // e.g., 1=Active, 0=Cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
