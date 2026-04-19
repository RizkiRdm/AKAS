<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Product;
use App\Domain\Models\Sale;
use Illuminate\Support\Facades\DB;

final class SaleService
{
    /**
     * Create sale transaction.
     *
     * @param array{
     *   shift_id: int,
     *   user_id: int,
     *   items: array<int, array{product_id: int, qty: int}>,
     *   payment_method: string,
     *   payment_ref: ?string
     * } $data
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $productIds = collect($data['items'])->pluck('product_id')->unique()->toArray();

            // Race condition protection: Lock product rows
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $saleItems = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    throw new \Exception("Product ID {$item['product_id']} not found.");
                }

                if ($product->stok < $item['qty']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Requested: {$item['qty']}, Available: {$product->stok}");
                }

                $subtotal = (float) $product->price * $item['qty'];
                $total += $subtotal;

                $saleItems[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                // Update stock
                $product->decrement('stok', $item['qty']);
            }

            $sale = Sale::create([
                'shift_id' => $data['shift_id'],
                'user_id' => $data['user_id'],
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_ref' => $data['payment_ref'] ?? null,
                'payment_status' => 'completed',
            ]);

            $sale->items()->createMany($saleItems);

            return $sale;
        });
    }
}
