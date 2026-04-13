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
        Schema::create('products', function (Blueprint $table) {
            $table->string('id_brg', 20)->primary();
            $table->string('nama_brg', 255);
            $table->string('id_kat', 10);
            $table->string('id_satuan', 10);
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->timestamps();

            $table->foreign('id_kat')->references('id_kat')->on('categories');
            $table->foreign('id_satuan')->references('id_satuan')->on('units');
        });

        // Add CHECK constraint manually for stok >= 0
        DB::statement('ALTER TABLE products ADD CONSTRAINT products_stok_check CHECK (stok >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
