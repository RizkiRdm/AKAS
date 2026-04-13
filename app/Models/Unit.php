<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $primaryKey = 'id_satuan';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['id_satuan', 'nama_satuan'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_satuan', 'id_satuan');
    }
}
