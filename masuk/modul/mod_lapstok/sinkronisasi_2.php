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

$tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail where date(waktu)='2025-06-18' and tipe=1 ");
$no=1;

while ($r=mysqli_fetch_array($tampil_barang)) {
            
    
        mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET hrgjual_barang='$r[hrgjual_dtrbmasuk]',
                                                                    hrgjual_barang1='$r[hrgjual_dtrbmasuk]'
                                                 where kd_barang ='$r[kd_barang]' ");

$no++;
}
        header('location:../../media_admin.php?module=trkasir');
    
}
?>
