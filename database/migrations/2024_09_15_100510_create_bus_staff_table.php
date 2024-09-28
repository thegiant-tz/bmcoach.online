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
        Schema::create('bus_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('driver_id')->constrained('users', 'id')->cascadeOnUpdate();
            $table->foreignId('cond_id')->nullable()->constrained('users', 'id')->cascadeOnUpdate();
            $table->foreignId('tingo_id')->nullable()->constrained('users', 'id')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_staff');
    }
};
