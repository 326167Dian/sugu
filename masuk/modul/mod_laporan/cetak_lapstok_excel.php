<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Lapstok.xls");

?>
<center>
    <h1>LAPORAN STOK 30 HARI TERAKHIR</h1>
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
        <th>T30</th>
        <th>T60</th>
        <th>GR (%)</th>
        <th>Q30</th>
        <th>Satuan</th>
        <th>Harga Beli</th>
        <th>Nilai Barang</th>
    </tr>
    <?php
    // koneksi database
    include "../../../configurasi/koneksi.php";
    // menampilkan data barang
    $data = $db->query("select * from barang");
    $no = 1;
    $total = [];
    while ($d = $data->fetch(PDO::FETCH_ASSOC)) {
        $nb = round(($d['stok_barang'] * $d['hrgsat_barang']),0);
        $hrgbeli = round($d['hrgsat_barang'],0);
        $total[]=$nb;
    ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $d['kd_barang']; ?></td>
            <td><?php echo $d['nm_barang']; ?></td>
            <td><?php echo $d['stok_barang']; ?></td>
            <td><?php echo $d['t30'] ?? 0; ?></td>
            <td><?php echo $d['t60'] ?? 0; ?></td>
            <td><?php echo $d['gr'] ?? 0; ?></td>
            <td><?php echo $d['q30'] ?? 0; ?></td>
            <td><?php echo $d['sat_barang']; ?></td>
            <td><?php echo $hrgbeli; ?></td>
            <td><?php echo $nb; ?></td>
        </tr>
    <?php
    }
    $jml=array_sum($total);
    ?>
    <tr>
        <td colspan="10" style="text-align:right;">Total Nilai Barang</td>  <td><?= format_rupiah($jml) ?></td>
    </tr>
</table>



