<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('name', 255);
            $table->string('sku', 50)->unique();
            $table->decimal('price', 15, 2);
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE products ADD CONSTRAINT products_stok_check CHECK (stok >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
