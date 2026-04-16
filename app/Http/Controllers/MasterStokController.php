<?php

namespace App\Http\Controllers;

use App\Domain\Models\Category;
use App\Domain\Models\Product;
use App\Domain\Models\Supplier;
use App\Domain\Models\Unit;
use App\Domain\Services\StockService;
use App\Http\Requests\MasterStok\StoreCategoryRequest;
use App\Http\Requests\MasterStok\StoreProductRequest;
use App\Http\Requests\MasterStok\StoreStockInRequest;
use App\Http\Requests\MasterStok\StoreSupplierRequest;
use App\Http\Requests\MasterStok\StoreUnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterStokController extends Controller
{
    public function __construct(protected StockService $stockService) {}

    public function index(Request $request)
    {
        $products = Product::with(['category', 'unit', 'supplier'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(15);

        if ($request->ajax()) {
            return view('master-stok._table_rows', compact('products'))->render();
        }

        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();

        return view('master-stok.index', compact('products', 'categories', 'units', 'suppliers'));
    }

    // Product CRUD
    public function storeProduct(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            $initialStock = $data['initial_stock'] ?? 0;
            unset($data['initial_stock']);

            $product = Product::create($data);

            if ($initialStock > 0) {
                $this->stockService->recordStockIn([
                    'product_id' => $product->id,
                    'qty' => $initialStock,
                    'note' => 'Stok awal saat pembuatan produk'
                ]);
            }

            return back()->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan produk.');
        }
    }

    public function updateProduct(StoreProductRequest $request, $product)
    {
        if (!$product instanceof Product) {
            $product = Product::findOrFail($product);
        }
        try {
            $product->update($request->validated());
            return back()->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui produk.');
        }
    }

    public function destroyProduct($product)
    {
        if (!$product instanceof Product) {
            $product = Product::findOrFail($product);
        }
        try {
            $product->delete();
            return back()->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk.');
        }
    }

    // Category CRUD
    public function storeCategory(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(StoreCategoryRequest $request, $category)
    {
        if (!$category instanceof Category) {
            $category = Category::findOrFail($category);
        }
        $category->update($request->validated());
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    // Unit CRUD
    public function storeUnit(StoreUnitRequest $request)
    {
        Unit::create($request->validated());
        return back()->with('success', 'Satuan berhasil ditambahkan.');
    }

    // Supplier CRUD
    public function storeSupplier(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    // Stock In
    public function storeStockIn(StoreStockInRequest $request)
    {
        try {
            $this->stockService->recordStockIn($request->validated());
            return back()->with('success', 'Stok berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error recording stock in: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan stok.');
        }
    }
}
