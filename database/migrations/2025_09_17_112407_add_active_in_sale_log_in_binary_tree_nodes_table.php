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
        Schema::table('binary_tree_nodes', function (Blueprint $table) {
            $table->integer('active_in_sale_log')->nullable()->default('1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binary_tree_nodes', function (Blueprint $table) {
            //
        });
    }
};
