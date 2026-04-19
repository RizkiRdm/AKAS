<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('starting_float', 15, 2)->default(0);
            $table->decimal('ending_cash', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->default(0);
            // variance(GENERATED) = ending_cash - expected_cash
            $table->decimal('variance', 15, 2)->storedAs('ending_cash - expected_cash')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE shifts ADD CONSTRAINT shifts_status_check CHECK (status IN ('open', 'closed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
