<?php

namespace App\Domain\Observers;

use App\Domain\Models\StockIn;

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
