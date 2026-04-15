<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('qty');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE stock_in ADD CONSTRAINT stock_in_qty_check CHECK (qty > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_in');
    }
};
