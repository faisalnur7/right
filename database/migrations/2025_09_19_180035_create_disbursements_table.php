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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('prime_transaction_id')->nullable();
            $table->integer('business_day');
            $table->date('disburse_date')->nullable();
            $table->tinyInteger('account_type')->nullable(); // e.g. bKash, Nagad, etc.
            $table->string('account_number')->nullable();

            $table->decimal('leads_amount', 15, 2)->default(0);
            $table->decimal('affiliate_amount', 15, 2)->default(0);
            $table->decimal('subscription_amount', 15, 2)->default(0);
            $table->decimal('associate_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prime_transaction_id')->references('id')->on('prime_transactions')->onDelete('set null');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
