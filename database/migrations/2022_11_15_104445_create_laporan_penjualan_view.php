<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(
            "CREATE VIEW view_laporan_penjualan AS

            SELECT 
                transaksis.id_transaksi,
                (select name from users where transaksis.user_id = users.id) as user,
                (select namabarang from barangs where transaksis.barang_id = barangs.id) as namabarang,
                transaksis.harga_beli,
                transaksis.harga_jual,
                transaksis.qty,
                transaksis.total,
                transaksis.harga_jual - transaksis.harga_beli as untung_per_item,
                transaksis.qty * (transaksis.harga_jual - transaksis.harga_beli) as total_untung_per_item,
                transaksis.created_at
            FROM transaksis"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // \DB::statement($this->dropView());
    }
};
