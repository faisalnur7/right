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
            $table->tinyInteger('position')->nullable()->after('level')->comment('1 = left child, 2 = right child');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binary_tree_nodes', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
