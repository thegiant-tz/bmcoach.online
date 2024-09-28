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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users', 'id')->cascadeOnUpdate();
            $table->foreignId('bus_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('route_id')->constrained()->cascadeOnUpdate();
            $table->string('psg_name');
            $table->string('seat_no');
            $table->double('fare');
            $table->string('psg_phone')->nullable();
            $table->timestamp('dep_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
