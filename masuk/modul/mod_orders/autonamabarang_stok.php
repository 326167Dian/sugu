<?php
include "../../../configurasi/koneksi.php";

// Sama seperti autonamabarang.php, tapi ikut mengirim stok_barang & sat_barang supaya bisa
// ditampilkan di sebelah nama barang pada dropdown pencarian (dipakai khusus di modul Order,
// tidak dipakai bareng dengan mod_lapstok/stok_kritis.php yang juga menggunakan autonamabarang.php
// versi lama dengan format array string biasa).
$key = $_POST['query'];

$stmt = $db->prepare("SELECT nm_barang, stok_barang, sat_barang FROM barang WHERE nm_barang LIKE ?");
$stmt->execute(["%$key%"]);

$json = [];
while ($re = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $json[] = array(
        'nm_barang'   => $re['nm_barang'],
        'stok_barang' => $re['stok_barang'],
        'sat_barang'  => $re['sat_barang'],
    );
}
echo json_encode($json);
