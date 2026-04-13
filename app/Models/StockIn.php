<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    use HasFactory;

    protected $table = 'stock_in';

    protected $fillable = [
        'tgl_masuk',
        'id_supplier',
        'id_brg',
        'jumlah',
        'total_harga',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_brg', 'id_brg');
    }
}
