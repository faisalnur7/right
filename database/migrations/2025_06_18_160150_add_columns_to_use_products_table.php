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
        Schema::table('use_products', function (Blueprint $table) {
            $table->string('validity')->nullable()->after('remarks');
            $table->integer('unit_per_day')->nullable()->after('validity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('use_products', function (Blueprint $table) {
            //
        });
    }
};
