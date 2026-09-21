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
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('referer_id')->nullable();
            $table->string('affiliate_id')->nullable();
            $table->integer('doc_type')->nullable()->comment("1 => NID, 2 => BC, 3 => PASSPORT");
            $table->string('document_file')->nullable();
            $table->string('father')->nullable();
            $table->string('mother')->nullable();
            $table->date('dob')->nullable();

            $table->unsignedBigInteger('permanent_division_id')->nullable();
            $table->unsignedBigInteger('permanent_district_id')->nullable();
            $table->unsignedBigInteger('permanent_police_station_id')->nullable();
            $table->unsignedBigInteger('permanent_post_office_id')->nullable();
            $table->string('permanent_post_code')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->unsignedBigInteger('present_division_id')->nullable();
            $table->unsignedBigInteger('present_district_id')->nullable();
            $table->unsignedBigInteger('present_police_station_id')->nullable();
            $table->unsignedBigInteger('present_post_office_id')->nullable();
            $table->string('present_post_code')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('is_same_address')->nullable();

            // Withdrawal information
            $table->integer('account_type')->nullable()->comment("1 => Bkash, 2 => Nagad, 3 => Rocket");
            $table->string('account_number')->nullable();

            // Prime affiliate informations
            $table->string('postal_code')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_nid')->nullable();
            $table->string('relation')->nullable();
            $table->string('package')->nullable();

            $table->timestamps();

            // Foreign key constrains
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referer_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreign('permanent_division_id')->references('id')->on('divisions');
            $table->foreign('permanent_district_id')->references('id')->on('districts');
            $table->foreign('permanent_police_station_id')->references('id')->on('police_stations');
            $table->foreign('permanent_post_office_id')->references('id')->on('post_offices');

            $table->foreign('present_division_id')->references('id')->on('divisions');
            $table->foreign('present_district_id')->references('id')->on('districts');
            $table->foreign('present_police_station_id')->references('id')->on('police_stations');
            $table->foreign('present_post_office_id')->references('id')->on('post_offices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};
