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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tgl_jual')->useCurrent();
            $table->string('id_brg', 20);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->integer('jumlah');
            $table->decimal('total_bayar', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_ref', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_brg')->references('id_brg')->on('products');
        });

        DB::statement('ALTER TABLE sales ADD CONSTRAINT sales_jumlah_check CHECK (jumlah > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
