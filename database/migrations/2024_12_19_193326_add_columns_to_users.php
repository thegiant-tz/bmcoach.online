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
        Schema::table('users', function (Blueprint $table) {
            $table->string('currency')->after('role_id')->nullable();
            $table->string('reg_no')->after('currency')->nullable();
            $table->string('tin')->after('reg_no')->nullable();
            $table->boolean('status')->after('tin')->default(true);
            $table->enum('pay_type', ['POSTPAID', 'PREPAID'])->after('status')->default('POSTPAID');
            $table->enum('agent_class', ['Normal', 'Private'])->after('pay_type')->default('Normal');
            $table->foreignId('boarding_point_id')->after('reg_no')->nullable()->constrained('boarding_points', 'id')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
