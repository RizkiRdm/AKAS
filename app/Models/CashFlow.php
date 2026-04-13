<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlow extends Model
{
    use HasFactory;

    protected $table = 'cash_flow';

    protected $fillable = [
        'shift_id',
        'tgl_flow',
        'keterangan',
        'masuk',
        'keluar',
    ];

    protected $casts = [
        'tgl_flow' => 'datetime',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
