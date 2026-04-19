<?php

namespace App\Domain\Services;

use App\Domain\Models\Product;
use App\Domain\Models\StockIn;
use App\Domain\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record stock in and update product stock.
     */
    public function recordStockIn(array $data): StockIn
    {
        return DB::transaction(function () use ($data) {
            $stockIn = StockIn::create([
                'product_id' => $data['product_id'],
                'user_id' => auth()->id() ?? User::first()?->id ?? 1, // Fallback for testing/console
                'qty' => $data['qty'],
                'note' => $data['note'] ?? null,
            ]);

            // StockInObserver will handle product stock increment

            return $stockIn;
        });
    }

    /**
     * Get low stock products (threshold < 10 by default as per DESIGN.md 7.1)
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::where('stok', '<', $threshold)->get();
    }
}
