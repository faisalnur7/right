<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prime_transactions', function (Blueprint $table) {
            // Filled by CommissionDistributionService; NULL for older rows.
            $table->unsignedBigInteger('sale_log_id')->nullable()->after('source_user_id')->index();
            $table->unsignedTinyInteger('level')->nullable()->after('sale_log_id');
        });
    }

    public function down(): void
    {
        Schema::table('prime_transactions', function (Blueprint $table) {
            $table->dropColumn(['sale_log_id', 'level']);
        });
    }
};
