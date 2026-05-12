<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_barang.xls");

?>
<center>
    <h1>STOK KRITIS </h1>
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
        <th>Satuan</th>
        <th>t30</th>
        <th>q30</th>
        
        <th>Harga Beli</th>
        
    </tr>
    <?php
    // koneksi database
    include "../../../configurasi/koneksi.php";
    // menampilkan data barang
    $data = $db->query("select * from barang where t30>0 order by t30 desc ");
    $no = 1;
    while ($d = $data->fetch(PDO::FETCH_ASSOC)) {
    $angka = intval($d['kd_barang']);
    ?>
        <tr>
            <td style='text-align:center;'><?php echo $no; ?></td>
            <td><?php echo $angka; ?></td>
            <td><?php echo $d['nm_barang']; ?></td>
            <td style='text-align:center;'><?php echo $d['stok_barang']; ?></td>
            <td style='text-align:center;'><?php echo $d['sat_barang']; ?></td>
            <td style='text-align:center;'><?php echo $d['t30']; ?></td>
            <td style='text-align:center;'><?php echo $d['q30']; ?></td>
            <td><?php echo $d['hrgsat_barang']; ?></td>
            
        </tr>
        
    <?php
    $no++;
    }
    ?>
</table>



?>