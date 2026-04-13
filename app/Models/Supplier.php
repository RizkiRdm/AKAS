<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $primaryKey = 'id_supplier';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['id_supplier', 'nama_supplier', 'alamat', 'no_telp'];

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'id_supplier', 'id_supplier');
    }
}
