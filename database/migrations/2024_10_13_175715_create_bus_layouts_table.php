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
        Schema::create('bus_layouts', function (Blueprint $table) {
            $table->id();
            $table->enum('label', ['2x1', '2x2', '2x2_toilet', '2x2_toilet_full']);
            $table->string('capacity');
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_layouts');
    }
};
