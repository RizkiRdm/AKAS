<?php

namespace App\Domain\Observers;

use App\Domain\Models\StockIn;
use App\Domain\Models\Product;

class StockInObserver
{
    /**
     * Handle the StockIn "created" event.
     */
    public function created(StockIn $stockIn): void
    {
        $product = $stockIn->product;
        $product->increment('stok', $stockIn->qty);
    }
}
