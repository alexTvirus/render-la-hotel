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
        Schema::create('packet_image', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('image_type_id')->nullable();
            $table->bigInteger('packet_id')->nullable();
            $table->string('url')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('packet_image');
    }
};
