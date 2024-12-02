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
            $table->foreignId('route_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('onboarded_by')->nullable()->constrained('users', 'id')->cascadeOnUpdate();
            $table->foreignId('offboarded_by')->nullable()->constrained('users', 'id')->cascadeOnUpdate();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('sender_email')->nullable();
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('item_name');
            $table->double('weight')->nullable();
            $table->double('size')->nullable();
            $table->double('item_value');
            $table->double('amount');
            $table->enum('status', ['pending', 'in transit', 'destinated', 'delivered'])->default('pending');
            $table->timestamp('dep_date')->default(now());
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
