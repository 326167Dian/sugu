<?php
include_once '../../../configurasi/koneksi.php';
include_once 'brgkategori-serverside.inc.php';

// Barang SLOW: 1 - 5 transaksi dalam periode
if ($_GET['action'] == "table_data") {
    echo json_encode(kategori_barang_serverside($db, 1, 5));
}
