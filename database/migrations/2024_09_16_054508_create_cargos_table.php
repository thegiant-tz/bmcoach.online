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
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('codeId');
            $table->string('descriptions');
            $table->string('sender');
            $table->string('receiver');
            $table->string('sender_phone');
            $table->string('receiver_phone');
            $table->string('pickup');
            $table->string('destination');
            $table->double('weight')->nullable();
            $table->double('dimension')->nullable();
            $table->enum('status', ['pending', 'in transit', 'destinated', 'delivered'])->default('pending');
            $table->timestamp('delivery_date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
