<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();

//            $table->dateTime('publish_at')->nullable();
//            $table->dateTime('free_at')->nullable();
            $table->decimal('base_price', 16, 3)->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('max_occupancy')->nullable();
            $table->integer('room_size')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('sleeps')->nullable();


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
        Schema::dropIfExists('room_types');
    }

};
