<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_jurnal_pembayaran_distributor.php";

$module     = $_GET['module'];
$act        = $_GET['act'];
$count      = $_POST['check'];
$tgl_lunas  = date('Y-m-d', time());
$petugas    = $_SESSION['namalengkap'];

for ($i = 0; $i < count($count); $i++) {
    echo $count[$i] . '<br>';

    $cektrbmasuklama = $db->prepare("SELECT carabayar, nm_supplier, ttl_trbmasuk FROM trbmasuk WHERE kd_trbmasuk = ?");
    $cektrbmasuklama->execute([$count[$i]]);
    $rtrbmasuklama = $cektrbmasuklama->fetch(PDO::FETCH_ASSOC);

    $stmt_update = $db->prepare("UPDATE trbmasuk SET
                                                carabayar       = ?,
                                                tgl_lunas       = ?,
                                                petugas_lunas   = ?
                                                WHERE kd_trbmasuk = ?");
    $stmt_update->execute(['LUNAS', $tgl_lunas, $petugas, $count[$i]]);

    if (!empty($rtrbmasuklama) && $rtrbmasuklama['carabayar'] != 'LUNAS') {
        catat_jurnal_pembayaran_distributor($db, $count[$i], $rtrbmasuklama['nm_supplier'], $rtrbmasuklama['ttl_trbmasuk'], $petugas);
    }
}

header('location:../../media_admin.php?module=trbmasukpbf');
