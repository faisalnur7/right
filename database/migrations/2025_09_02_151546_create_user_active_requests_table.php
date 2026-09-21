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
        Schema::create('user_active_requests', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('present_reference_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('new_reference_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('sale_log_id')->constrained('sale_logs')->onDelete('cascade');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_active_requests');
    }
};
