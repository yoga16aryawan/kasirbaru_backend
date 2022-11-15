<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'barang_id',
        'qty',
        'harga_jual',
    ];

    public function getTotalAttribute()
    {
        return $this->qty * $this->harga_jual;
    }

    protected $appends = ['Total'];

    public function barangs()
    {
        return $this->belongsTo(Barang::class);
    }
}
