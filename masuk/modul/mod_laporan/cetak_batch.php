<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_barang.xls");

?>
<center>
    <h1>DATABASE BARANG APOTEK BERDASARKAN BATCH AKTIF</h1>
</center>
<?php
$tgl_awal = date('d-m-Y');
echo "Dicetak Oleh : ";

echo $_SESSION['namalengkap'];
echo "  Tanggal : ";
echo $tgl_awal; ?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Stok Barang</th>
        <th>Kode Batch</th>
        <th>Exp Date</th>
        <th>masuk</th>
        <th>keluar</th>
        <th>sisa</th>
        <th>Satuan</th>
        <th>Harga Beli</th>
        <th>Harga Jual Reguler</th>
        <th>Harga Jual Grab Health</th>
        <th>Harga Jual Grab Halodoc</th>
        <th>Harga Jual Grab Market Place</th>

    </tr>
    <?php
    // koneksi database
    include "../../../configurasi/koneksi.php";
    // menampilkan data barang
    $data = $db->query("SELECT 
            batch.no_batch, 
            batch.exp_date, 
            barang.kd_barang, 
            barang.nm_barang, 
            barang.sat_barang,
            barang.stok_barang,
            barang.hrgsat_barang,
            barang.hrgjual_barang,
            barang.hrgjual_barang1,
            barang.hrgjual_barang2
     from batch join barang on(batch.kd_barang=barang.kd_barang) where batch.no_batch !='' GROUP by no_batch order by barang.nm_barang asc");
    $no = 1;
    while ($d = $data->fetch_array()) {
        $masuk = $db->query("SELECT sum(qty) as msk FROM batch WHERE no_batch = '$d[no_batch]' and status = 'masuk'")->fetch_array();
        $keluar = $db->query("SELECT sum(qty) as klr FROM batch WHERE no_batch = '$d[no_batch]' and status = 'keluar'")->fetch_array();
        $stok = $masuk['msk'] - $keluar['klr'];
        
        if($stok != 0){
            echo "
            <tr>
                <td style='text-align:center;'>$no</td>
                <td>$d[kd_barang]</td>
                <td>$d[nm_barang]</td>
                <td>$d[stok_barang]</td>
                <td style='text-align:center;'>$d[no_batch]</td>
                <td style='text-align:center;'>$d[exp_date]</td>
                <td style='text-align:center;'>$masuk[msk]</td>
                <td style='text-align:center;'>$keluar[klr]</td>
                <td style='text-align:center;'>$stok</td>
                <td style='text-align:center;'>$d[sat_barang]</td>
                <td style='text-align:center;'>$d[hrgsat_barang]</td>
                <td style='text-align:center;'>$d[hrgjual_barang]</td>
                <td style='text-align:center;'>$d[hrgjual_barang1]</td>
                <td style='text-align:center;'>$d[hrgjual_barang2]</td>
                <td style='text-align:center;'></td
            </tr>
            ";

        $no++;
        }
    }
    ?>
</table>



?>