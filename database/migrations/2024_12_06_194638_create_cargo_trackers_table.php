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
        Schema::create('cargo_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('respondent')->constrained('users', 'id')->cascadeOnUpdate();
            $table->foreignId('bus_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->enum('status', ['Pending', 'In Transit', 'Delivered'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_trackers');
    }
};
