<?php
session_start();
include "../../../configurasi/koneksi.php";

$module     = $_GET['module'];
$act        = $_GET['act'];
$count      = $_POST['check'];
$tgl_lunas  = date('Y-m-d', time());
$petugas    = $_SESSION['namalengkap'];

for ($i = 0; $i < count($count); $i++) {
    echo $count[$i] . '<br>';

    $stmt_update = $db->prepare("UPDATE trbmasuk SET 
                                                carabayar       = ?,
                                                tgl_lunas       = ?,
                                                petugas_lunas   = ?
                                                WHERE kd_trbmasuk = ?");
    $stmt_update->execute(['LUNAS', $tgl_lunas, $petugas, $count[$i]]);
}

header('location:../../media_admin.php?module=trbmasukpbf');
