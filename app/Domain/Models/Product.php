<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $lastProduct = static::latest('id')->first();
                $number = $lastProduct ? (int) substr($lastProduct->sku, 4) + 1 : 1;
                $product->sku = 'BRG-'.str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'category_id',
        'unit_id',
        'supplier_id',
        'name',
        'sku',
        'price',
        'purchase_price',
        'stok',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }
}
