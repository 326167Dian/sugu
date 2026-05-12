<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
  include "../../../configurasi/koneksi.php";
  include "../../../configurasi/fungsi_thumb.php";
  include "../../../configurasi/library.php";

  $module = $_GET['module'];
  $act = $_GET['act'];

  // Input admin
  if ($module == 'carabayar' and $act == 'input_carabayar') {

    $stmt = $db->prepare("SELECT COUNT(*) FROM carabayar WHERE nm_carabayar = ?");
    $stmt->execute([$_POST['nm_carabayar']]);
    $ada = $stmt->fetchColumn();
    if ($ada > 0) {
      echo "<script type='text/javascript'>alert('Jenis sudah ada!');history.go(-1);</script>";
    } else {

      $stmt = $db->prepare("INSERT INTO carabayar(nm_carabayar) VALUES (?)");
      $stmt->execute([$_POST['nm_carabayar']]);


      //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
      header('location:../../media_admin.php?module=' . $module);
    }
  }
  //updata carabayar
  elseif ($module == 'carabayar' and $act == 'update_carabayar') {

    $stmt = $db->prepare("UPDATE carabayar SET nm_carabayar = ? WHERE id_carabayar = ?");
    $stmt->execute([$_POST['nm_carabayar'], $_POST['id']]);

    //echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
    header('location:../../media_admin.php?module=' . $module);
  }
  //Hapus Proyek
  elseif ($module == 'carabayar' and $act == 'hapus') {

    $stmt = $db->prepare("DELETE FROM carabayar WHERE id_carabayar = ?");
    $stmt->execute([$_GET['id']]);
    //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
    header('location:../../media_admin.php?module=' . $module);
  }
}
