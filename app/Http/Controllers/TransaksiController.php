<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cart;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    public function carts()
    {
        try {
            $carts = DB::table('carts')
                ->join('barangs', 'barangs.id', '=', 'carts.barang_id')
                ->select('carts.id', 'carts.qty', 'carts.user_id', 'barangs.namabarang', 'carts.harga_jual')
                ->orderby('id', 'desc')
                ->get();
            $carts = collect($carts);
            $carts_collection = $carts->where('user_id', Auth::user()->id);
            if ($carts_collection->count() == 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'cart kosong',
                    'total_data' => $carts_collection->count(),
                    'data' => $carts_collection
                ], 200);
            }
            return response()->json([
                'status' => true,
                'message' => 'data ditemukan',
                'total_data' => $carts_collection->count(),
                'data' => $carts_collection
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function addCart(Request $request)
    {
        try {
            $barang = Barang::where('barcode', $request->barcode)->first();
            if ($barang == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'data barang tidak ditemukan',
                ], 200);
            }
            if (($barang->stok - $request->qty) < 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok pada sistem tersisa ' . $barang->stok
                ], 200);
            }
            $previous_cart = Cart::where('user_id', '=', Auth::user()->id)
                ->where('barang_id', '=', $barang->id)
                ->first();
            if ($previous_cart == null) {
                Cart::create([
                    'user_id' => Auth::user()->id,
                    'barang_id' => $barang->id,
                    'qty' => $request->qty,
                    'harga_jual' => $barang->harga_jual
                ]);
            } else {
                $previous_cart->fill([
                    'qty' => $previous_cart->qty + $request->qty
                ])->save();
            }
            $cart = DB::table('carts')
                ->join('barangs', 'barangs.id', '=', 'carts.barang_id')
                ->select('carts.id', 'carts.qty', 'carts.user_id', 'barangs.namabarang', 'carts.harga_jual')
                ->where('carts.user_id', '=', Auth::user()->id)
                ->orderby('id', 'desc')
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'berhasil menambahkan cart',
                'data' => $cart
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function simpanTransaksi()
    {
        try {
            $carts = collect(Cart::all());
            $filteredCart = $carts->where('user_id', Auth::user()->id);
            $id_transaksi = Str::random(5);
            if ($filteredCart->count() < 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'carts kosong',
                    'total_data' => $filteredCart->count(),
                    'data' => $filteredCart
                ], 200);
            }
            foreach ($filteredCart as $f) {
                $barang = Barang::where('id', $f->barang_id)->first();
                Transaksi::create([
                    'id_transaksi' => $id_transaksi,
                    'user_id' => $f->user_id,
                    'barang_id' => $f->barang_id,
                    'qty' => $f->qty,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $f->harga_jual,
                    'total' => $f->total
                ]);
                $barang->fill([
                    'stok' => $barang->stok - $f->qty
                ])->save();
            }
            DB::table('carts')->where('user_id', Auth::user()->id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Transaksi Berhasil disimpan',
                'total_data' => $filteredCart->count()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function search()
    {
    }
}
