<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'barcode',
        'namabarang',
        'harga_beli',
        'harga_jual',
        'stok',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
