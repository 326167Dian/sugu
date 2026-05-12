<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Jurnal_Kas.xls");

?>
<center>
    <h1>JURNAL KAS</h1>
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
        <th>id Jurnal</th>
        <th>tanggal</th>
        <th>Keterangan</th>
        <th>Petugas</th>
        <th>Jenis Transaksi</th>
        <th>Debit</th>
        <th>Kredit</th>
        <th>Cara Bayar</th>
        <th>Waktu Input</th>
        
    </tr>
    <?php
    // koneksi database
    include "../../../configurasi/koneksi.php";
    // menampilkan data barang
    $data = $db->query("SELECT * FROM jurnal");
    $no = 1;
    while ($d = $data->fetch(PDO::FETCH_ASSOC)) {
    $transaksi = $db->query("SELECT * FROM jenis_jurnal WHERE idjenis = '$d[idjenis]'");
    $jenis = $transaksi->fetch(PDO::FETCH_ASSOC);
    ?>
        <tr>
            <td style='text-align:center;'><?= $no ?></td>
            <td><?= $d['id_jurnal'] ?></td>
            <td><?= $d['tanggal'] ?></td>
            <td style='text-align:center;'><?= $d['ket'] ?></td>
            <td style='text-align:center;'><?= $d['petugas'] ?></td>
            <td style='text-align:right;'><?= $jenis['nm_jurnal'] ?></td>
            <td style='text-align:right;'><?= format_rupiah($d['debit']) ?></td>
            <td style='text-align:right;'><?= format_rupiah($d['kredit']) ?></td>
            <td style='text-align:center;'><?= $d['carabayar'] ?></td>
            <td style='text-align:center;'><?= $d['current'] ?></td>            
        </tr>
        
    <?php
    $no++;
    }
    ?>
</table>



?>