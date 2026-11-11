<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('binary_tree_nodes', function (Blueprint $table) {
            // 0 = basic ID, 1 = clone ID (re-use placement under a basic ID)
            $table->boolean('is_clone')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('binary_tree_nodes', function (Blueprint $table) {
            $table->dropColumn('is_clone');
        });
    }
};
