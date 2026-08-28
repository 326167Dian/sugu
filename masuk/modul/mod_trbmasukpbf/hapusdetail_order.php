<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

$id_dtrbmasuk  = $_POST['id_dtrbmasuk'];
$kd_barang     = isset($_POST['kd_barang']) ? $_POST['kd_barang'] : '';
$kd_trbmasuk_draft = isset($_POST['kd_trbmasuk']) ? $_POST['kd_trbmasuk'] : '';

header('Content-Type: application/json');

try {
    $db->beginTransaction();

// $trbmasuk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail WHERE kd_orders='$r[kd_trbmasuk]' AND id_barang='$r[id_barang]'");
$stmt = $db->prepare("SELECT * FROM trbmasuk_detail WHERE id_dtrbmasuk = ? AND kd_orders = ?");
$stmt->execute([$id_dtrbmasuk, $_POST['kd_orders']]);
$r1 = $stmt->fetch(PDO::FETCH_ASSOC);
$r1_num = $stmt->rowCount();

// id_dtrbmasuk yang dikirim browser adalah PK tabel trbmasuk_detail (baris di atas), BUKAN PK ordersdetail --
// jangan pernah dipakai untuk mencari baris ordersdetail (di cabang else di bawah), karena kedua tabel
// itu auto_increment sendiri-sendiri sehingga angkanya bisa kebetulan sama dengan PK barang lain di
// ordersdetail. kd_barang WAJIB dicocokkan juga supaya tidak salah menyentuh item lain.
if ($r1_num == 0 && $kd_barang === '') {
    throw new Exception('Data tidak lengkap untuk menghapus baris ini. Silakan muat ulang halaman.');
}

if ($r1_num > 0) {
    //update stok
    $stmt_stok = $db->prepare("SELECT id_barang, stok_barang, konversi FROM barang 
                                WHERE id_barang=?");
    $stmt_stok->execute([$r1['id_barang']]);
    $rst = $stmt_stok->fetch(PDO::FETCH_ASSOC);
    $stok_barang = $rst['stok_barang'];
    $stokakhir = $stok_barang - $r1['qty_dtrbmasuk'];

    $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $r1['id_barang']]);
    // dikembalikan jadi belum diterima ('1') supaya bisa diterima ulang lewat Cek Pesanan, bukan hilang/nyangkut
    $db->prepare("UPDATE ordersdetail SET masuk = '1' WHERE id_barang = ? AND kd_trbmasuk = ?")->execute([$r1['id_barang'], $r1['kd_orders']]);
    // Insert History Deleted
    
    $stmt_inser_log = $db->prepare("INSERT INTO trbmasuk_detail_hist (
                                                    kd_trbmasuk,
                                                    kd_orders,
                                                    id_barang,
                                                    kd_barang,
                                                    nmbrg_dtrbmasuk,
                                                    qty_dtrbmasuk,
                                                    sat_dtrbmasuk,
                                                    qty_grosir,
                                                    satgrosir_dtrbmasuk,
                                                    hnasat_dtrbmasuk,
                                                    diskon,
                                                    konversi,
                                                    hrgsat_dtrbmasuk,
                                                    hrgjual_dtrbmasuk,
                                                    hrgttl_dtrbmasuk,
                                                    no_batch,
                                                    exp_date,
                                                    waktu,
                                                    tipe
                                                    ) 
                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt_inser_log->execute([$r1['kd_trbmasuk'],
                                $r1['kd_orders'],
                                $r1['id_barang'],
                                $r1['kd_barang'],
                                $r1['nmbrg_dtrbmasuk'],
                                $r1['qty_dtrbmasuk'],
                                $r1['sat_dtrbmasuk'],
                                $r1['qty_grosir'],
                                $r1['satgrosir_dtrbmasuk'],
                                $r1['hnasat_dtrbmasuk'],
                                $r1['diskon'],
                                $r1['konversi'],
                                $r1['hrgsat_dtrbmasuk'],
                                $r1['hrgjual_dtrbmasuk'],
                                $r1['hrgttl_dtrbmasuk'],
                                $r1['no_batch'],
                                $r1['exp_date'],
                                $r1['waktu'],
                                $r1['tipe']
                            ]);
    
    $stmt_hapusdetail = $db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?");
    $stmt_hapusdetail->execute([$id_dtrbmasuk]);
    
    $stmt_hapusbatch = $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND kd_barang = ? AND no_batch = ?");
    $stmt_hapusbatch->execute([$r1['kd_trbmasuk'],$r1['kd_barang'],$r1['no_batch']]);
} else {
    // Baris ini bukan trbmasuk_detail (sudah dihapus/berubah) -- dicocokkan lewat kd_barang + kd_orders,
    // BUKAN id_dtrbmasuk (itu PK trbmasuk_detail, bukan PK ordersdetail, jadi tidak boleh dipakai langsung
    // untuk mencari baris di ordersdetail supaya tidak menyentuh item lain yang kebetulan PK-nya sama).
    // kd_orders di sini adalah KODE PESANAN, sesuai kolom ordersdetail.kd_trbmasuk.
    $stmt_update = $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE kd_barang = ? AND kd_trbmasuk = ?");
    $stmt_update->execute([$kd_barang, $_POST['kd_orders']]);
}

    $subtotal = hitung_subtotal_pbf($db, $kd_trbmasuk_draft);

    $db->commit();
    echo json_encode([
        'status'   => 'ok',
        'subtotal' => format_rupiah($subtotal)
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
