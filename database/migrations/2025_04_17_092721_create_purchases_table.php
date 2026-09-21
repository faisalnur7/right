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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_user_id')->constrained('admins')->onDelete('cascade');
            $table->string('reference_no')->nullable();
            $table->date('purchase_date');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('delivery_charge', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('attachment')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
