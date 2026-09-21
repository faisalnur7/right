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
        Schema::table('sale_logs', function (Blueprint $table) {
            $table->float('total_commission')->nullable()->after('name');
            $table->float('tier1_percentage')->nullable()->after('total_commission');
            $table->float('tier2_percentage')->nullable()->after('tier1_percentage');
            $table->float('total_use_commission')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_logs', function (Blueprint $table) {
            //
        });
    }
};
