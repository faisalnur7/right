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
        Schema::create('package_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('subscription_package_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('payment_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('transaction_number')->nullable();
            $table->string('transaction_mobile_number')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('assigned_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('is_verified')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_users');
    }
};
