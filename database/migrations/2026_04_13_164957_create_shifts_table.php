<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->decimal('starting_float', 15, 2)->default(0);
            $table->decimal('calculated_cash_flow', 15, 2)->default(0);
            $table->decimal('ending_cash', 15, 2)->nullable();
            // Variance: (ending_cash - (starting_float + calculated_cash_flow))
            $table->decimal('variance', 15, 2)->storedAs('ending_cash - (starting_float + calculated_cash_flow)');
            $table->string('status', 20)->default('open');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE shifts ADD CONSTRAINT shifts_status_check CHECK (status IN ('open', 'closed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
