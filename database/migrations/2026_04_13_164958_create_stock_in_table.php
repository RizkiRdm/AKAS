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
        Schema::create('stock_in', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_masuk')->useCurrent();
            $table->string('id_supplier', 10);
            $table->string('id_brg', 20);
            $table->integer('jumlah');
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();

            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers');
            $table->foreign('id_brg')->references('id_brg')->on('products');
        });

        DB::statement('ALTER TABLE stock_in ADD CONSTRAINT stock_in_jumlah_check CHECK (jumlah > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in');
    }
};
