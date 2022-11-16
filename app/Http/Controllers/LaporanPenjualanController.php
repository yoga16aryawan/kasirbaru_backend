<?php

namespace App\Http\Controllers;

use App\Models\ViewLaporanPenjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LaporanPenjualanController extends Controller
{
    public function laporanPenjualan(Request $request)
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your Account Unauthorized to access this information'
                ], 401);
            }
            $start = Carbon::createFromFormat('Y-m-d', $request->start)->format('d-m-Y');
            $end = Carbon::createFromFormat('Y-m-d', $request->end)->format('d-m-Y');
            $dataLaporan = ViewLaporanPenjualan::whereDate('created_at', '>=', $request->start)
                ->whereDate('created_at', '<=', $request->end)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Laporan Penjualan dari ' . $start . ' sampai ' . $end,
                'total_data' => $dataLaporan->count(),
                'total_penjualan' => $dataLaporan->sum('total'),
                'total_untung' => $dataLaporan->sum('total_untung_per_item'),
                'data' => $dataLaporan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
