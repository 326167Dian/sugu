<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_stokopname.xls");

?>
<center>
    <h1>LAPORAN STOK OPNAME</h1>
</center>
<?php
$tgl_awal = $_GET['tgl_awal'];
$tgl_akhir = $_GET['tgl_akhir'];
$shift = $_GET['shift'];
$tglskrg = date('Y-m-d');
echo "Dicetak Oleh : ";

echo $_SESSION['namalengkap'];
echo " Tanggal : $tglskrg";

?>
<table border="1">
    <tr style="text-align: center; font-weight: bold;">
        <td>No</td>
        <td>Petugas</td>
        <td>Kode Barang</td>
        <td>Nama Barang</td>
        <td>Satuan</td>
        <td>Expire Date</td>
        <td>Jumlah ED</td>
        <td>Stok Komputer</td>
        <td>Stok Fisik</td> 
        <td>Selisih</td>
        <td>Waktu</td>
        <td>Harga</td>
        <td>Total</td>
    </tr>
    <?php
    $so = $db->prepare("select * from stok_opname 
      where shift=$shift and tgl_stokopname between '$tgl_awal' and '$tgl_akhir'");
    $so->execute();
    $no = 1;
    while ($lihat = $so->fetch(PDO::FETCH_ASSOC))
        { $obat = $db->prepare("select * from barang where kd_barang='$lihat[kd_barang]'");
          $obat->execute();    
          $tobat = $obat->fetch(PDO::FETCH_ASSOC);
          $admin = $db->prepare("select * from admin where id_admin = '$lihat[id_admin]'");
          $admin->execute();
          $tadmin = $admin->fetch(PDO::FETCH_ASSOC);
            echo"
        <tr>
         <td>$no</td>
         <td>$tadmin[nama_lengkap]</td>
         <td>$lihat[kd_barang]</td>
         <td>$tobat[nm_barang]</td>
         <td style='text-align:center;'>$tobat[sat_barang]</td>
         <td style='text-align:center;'>$lihat[exp_date]</td>
         <td style='text-align:center;'>$lihat[jml]</td>
         <td style='text-align:center;'>$lihat[stok_sistem]</td>
         <td style='text-align:center;'>$lihat[stok_fisik]</td>
         <td style='text-align:center;'>$lihat[selisih]</td>
         <td>$lihat[tgl_current]</td>
         <td>$lihat[hrgsat_barang]</td>
         <td>$lihat[ttl_hrgbrg]</td>
         
        </tr>
        ";
        $no++;
        }
    ?>
</table>



