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
        Schema::create('product_sale_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sale_log_id');

            $table->integer('unit')->nullable();
            $table->decimal('price', 10, 2)->nullable();

            // Foreign keys & cascade deletes
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');

            $table->foreign('sale_log_id')
                  ->references('id')->on('sale_logs')
                  ->onDelete('cascade');

            // Optional: prevent duplicate entries
            $table->unique(['product_id', 'sale_log_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sale_log');
    }
};
