<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id_brg';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_brg',
        'nama_brg',
        'id_kat',
        'id_satuan',
        'stok',
        'harga_beli',
        'harga_jual',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_kat', 'id_kat');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_satuan', 'id_satuan');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'id_brg', 'id_brg');
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'id_brg', 'id_brg');
    }
}
