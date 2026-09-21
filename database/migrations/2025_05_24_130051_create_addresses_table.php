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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');

            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('police_station_id')->nullable()->constrained('police_stations')->onDelete('set null');
            $table->foreignId('post_office_id')->nullable()->constrained('post_offices')->onDelete('set null');

            $table->string('address');
            $table->string('city');
            $table->string('zip');
            $table->integer('type')->nullable()->comment("1 => Billing address, 2=> Shipping address");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
