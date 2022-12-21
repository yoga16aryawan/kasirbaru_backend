<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Kuala_Lumpur');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $barang = Barang::latest()->get();
            $data = collect($barang);
            $total = $data->count();
            return response()->json([
                'status' => true,
                'total_data' => $total,
                'data' => $barang
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function list_barang()
    {
        try {
            $barang = Barang::all();
            $data = collect($barang);
            $total = $data->count();
            return response()->json([
                'status' => true,
                'total_data' => $total,
                'data' => $barang
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validateBarang = Validator::make(
                $request->all(),
                [
                    'barcode' => 'required',
                    'namabarang' => 'required',
                    'harga_beli' => 'required',
                    'harga_jual' => 'required',
                    'stok' => 'required'
                ]
            );

            if ($validateBarang->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateBarang->errors()
                ], 401);
            }
            $cekBarcode = Barang::where('barcode', $request->barcode)->get();
            if ($cekBarcode->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'barcode tersebut sudah terdata',
                    'data' => $cekBarcode
                ], 302);
            }
            $Barang = Barang::create([
                'barcode' => $request->barcode,
                'namabarang' => $request->namabarang,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual,
                'stok' => $request->stok
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan Barang'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $barang = Barang::find($id);
            if (empty($barang)) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'data ditemukan',
                'data' => $barang
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function edit(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $validateBarang = Validator::make(
                $request->all(),
                [
                    'barcode' => 'required',
                    'namabarang' => 'required',
                    'harga_beli' => 'required',
                    'harga_jual' => 'required',
                    'stok' => 'required'
                ]
            );

            if ($validateBarang->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateBarang->errors()
                ], 401);
            }


            $barang = Barang::find($id);
            if (empty($barang)) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan',
                ], 200);
            }

            $input = $request->all();
            $barang->fill($input)->save();

            $newbarang = Barang::find($id);

            return response()->json([
                'status' => true,
                'message' => 'Update Berhasil',
                'data' => $newbarang
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barang $barang)
    {
        //
    }

    public function searching(Request $request)
    {
        try {
            $validateBarang = Validator::make(
                $request->all(),
                [
                    'key' => 'required'
                ]
            );
            if ($validateBarang->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateBarang->errors()
                ]);
            }
            $barang = Barang::select('*')->where('barcode', $request->key)->orWhere('namabarang', 'LIKE', '%' . $request->key . '%')->get();
            if ($barang->count() == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan'
                ], 200);
            }
            return response()->json([
                'status' => true,
                'message' => 'data ditemukan',
                'total_data' => $barang->count(),
                'data' => $barang
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function singleSearching(Request $request)
    {
        try {
            $barang = Barang::where('barcode', $request->key)->limit(1)->get();
            return response()->json([
                'status' => true,
                'namabarang' => $barang[0]['namabarang'],
                'harga_jual' => $barang[0]['harga_jual']
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
