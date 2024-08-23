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
        Schema::create('amenity_room_type', function (Blueprint $table) {
            $table->id();

            $table->decimal('price', 16, 3)->nullable();
            $table->integer('quantity')->nullable();

            $table->bigInteger('room_type_id')->nullable();
            $table->bigInteger('amenity_id')->nullable();


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
        Schema::dropIfExists('amenity_room_type');
    }
};
