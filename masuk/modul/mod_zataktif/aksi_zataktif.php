<?php
session_start();
if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{
include "../../../configurasi/koneksi.php";
include "../../../configurasi/library.php";

$module = $_GET['module'];
$act = $_GET['act'];

$user = '';
if (isset($_SESSION['namauser']) && $_SESSION['namauser'] != '') {
    $user = $_SESSION['namauser'];
} elseif (isset($_SESSION['namalengkap']) && $_SESSION['namalengkap'] != '') {
    $user = $_SESSION['namalengkap'];
} else {
    $user = 'system';
}

if ($module=='zataktif' AND $act=='input_zataktif'){
    $stmt = $db->prepare("INSERT INTO zataktif(nm_zataktif, indikasi, aturanpakai, saran, user, updated_at)
                         VALUES(?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_POST['nm_zataktif'],
        $_POST['indikasi'],
        $_POST['aturanpakai'],
        $_POST['saran'],
        $user
    ]);

    header('location:../../media_admin.php?module='.$module);
}
elseif ($module=='zataktif' AND $act=='update_zataktif'){
    $stmt = $db->prepare("UPDATE zataktif SET nm_zataktif = ?,
                                    indikasi = ?,
                                    aturanpakai = ?,
                                    saran = ?,
                                    user = ?,
                                    updated_at = NOW()
                                    WHERE id_zataktif = ?");
    $stmt->execute([
        $_POST['nm_zataktif'],
        $_POST['indikasi'],
        $_POST['aturanpakai'],
        $_POST['saran'],
        $user,
        $_POST['id']
    ]);

    header('location:../../media_admin.php?module='.$module);
}
elseif ($module=='zataktif' AND $act=='hapus'){
    $stmt = $db->prepare("DELETE FROM zataktif WHERE id_zataktif = ?");
    $stmt->execute([$_GET['id']]);

    header('location:../../media_admin.php?module='.$module);
}

}
?>
