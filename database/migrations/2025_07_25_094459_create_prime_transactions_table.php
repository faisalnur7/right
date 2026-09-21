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
        Schema::create('prime_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable();
            $table->integer('type')->nullable();
            $table->foreignId('source_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount_in', 10, 2)->default(0.00)->nullable();
            $table->decimal('amount_out', 10, 2)->default(0.00)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prime_transactions');
    }
};
