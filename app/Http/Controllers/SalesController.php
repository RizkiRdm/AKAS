<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Models\Product;
use App\Domain\Models\Shift;
use App\Domain\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class SalesController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function pos(): View
    {
        $products = Product::with(['category', 'unit'])->get();
        return view('sales.pos', compact('products'));
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            // Find active shift for the user
            $shift = Shift::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if (!$shift) {
                return response()->json([
                    'message' => 'No active shift found. Please open a shift first.'
                ], 422);
            }

            $sale = $this->saleService->createSale([
                'shift_id' => $shift->id,
                'user_id' => auth()->id(),
                'items' => $request->validated('items'),
                'payment_method' => $request->validated('payment_method'),
                'payment_ref' => $request->validated('payment_ref'),
            ]);

            return response()->json([
                'message' => 'Sale created successfully',
                'sale' => $sale
            ], 201);

        } catch (\Exception $e) {
            Log::error('Sale creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
