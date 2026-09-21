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
            $table->dropColumn(['tier1_use_percentage', 'tier2_use_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_logs', function (Blueprint $table) {
            $table->float('tier1_use_percentage')->nullable()->default(0);
            $table->float('tier2_use_percentage')->nullable()->default(0);
        });
    }
};
