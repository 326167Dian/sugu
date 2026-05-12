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

  // tambah jenis obat
  if ($module == 'jenisobat' and $act == 'input_jenisobat') {

    $stmt = $db->prepare("SELECT COUNT(*) FROM jenis_obat WHERE jenisobat = ?");
    $stmt->execute([$_POST['jenisobat']]);
    $ada = $stmt->fetchColumn();

    if ($ada > 0) {
      echo "<script type='text/javascript'>alert('Jenis Obat Sudah Ada!');history.go(-1);</script>";
    } else {

      $stmt = $db->prepare("INSERT INTO jenis_obat(jenisobat,ket) VALUES(?, ?)");
      $stmt->execute([$_POST['jenisobat'], $_POST['ket']]);


      //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
      header('location:../../media_admin.php?module=' . $module);
    }
  }
  //edit jenis obat
  elseif ($module == 'jenisobat' and $act == 'edit') {

    $stmt = $db->prepare("UPDATE jenis_obat SET jenisobat = ?, ket = ? WHERE idjenis = ?");
    $stmt->execute([$_POST['jenisobat'], $_POST['ket'], $_POST['idjenis']]);

    //echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
    header('location:../../media_admin.php?module=' . $module);
  }
  //Hapus jenis obat
  elseif ($module == 'jenisobat' and $act == 'hapus') {

    $stmt = $db->prepare("DELETE FROM jenis_obat WHERE idjenis = ?");
    $stmt->execute([$_GET['id']]);
    //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
    header('location:../../media_admin.php?module=' . $module);
  }
}
