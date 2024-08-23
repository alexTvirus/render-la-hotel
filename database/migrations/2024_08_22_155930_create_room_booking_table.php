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
        Schema::create('room_booking', function (Blueprint $table) {
            $table->id();

//            $table->dateTime('publish_at')->nullable();
//            $table->dateTime('free_at')->nullable();
            $table->decimal('price', 16, 3)->nullable();


            $table->bigInteger('room_id')->nullable();
            $table->bigInteger('booking_id')->nullable();


            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_booking');
    }
};
