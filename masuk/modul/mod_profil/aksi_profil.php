<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_thumb.php";
include "../../../configurasi/library.php";

$module=$_GET['module'];
$act=$_GET['act'];


if ($module=='profil' AND $act=='update_profil'){
  if (empty($_POST['password'])) {
    $stmt = $db->prepare("UPDATE admin SET nama_lengkap   = ?,
                                  no_telp        = ?                
                           WHERE  id_admin     = ?");
    $stmt->execute([$_POST['nama_lengkap'], $_POST['no_telp'], $_POST['id']]);
  }
  // Apabila password diubah
  else{
    $pass = password_hash($_POST['password'],PASSWORD_BCRYPT);
    $stmt = $db->prepare("UPDATE admin SET 
                                        password        = ?,
                                        nama_lengkap    = ?,
                                        no_telp         = ?
                           WHERE id_admin      = ?");
    $stmt->execute([$pass, $_POST['nama_lengkap'], $_POST['no_telp'], $_POST['id']]);
  }
  header('location:../../media_admin.php?module='.$module);
}


}
?>
