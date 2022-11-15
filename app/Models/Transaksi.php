<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_transaksi',
        'user_id',
        'barang_id',
        'qty',
        'harga_beli',
        'harga_jual',
        'total'
    ];
}
