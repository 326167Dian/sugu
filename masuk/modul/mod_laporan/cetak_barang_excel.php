<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_barang.xls");

?>
<center>
    <h1>DATABASE BARANG APOTEK</h1>
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
        <th>No. Batch</th>
        <th>Stok Barang</th>
        <th>Satuan</th>
        <th>Jenis & Rak</th>
        <th>Harga Beli</th>
        <th>Harga Jual Reguler</th>
        <th>Harga Jual Dokter</th>
        <th>Harga Jual Halodoc</th>
        <th>Harga Jual Market place</th>
    </tr>
    <?php
    // koneksi database
    include "../../../configurasi/koneksi.php";
    // menampilkan data barang
    $data = $db->query("select * from barang order by nm_barang asc");
    $no = 1;
    $nomorbatch="";
    $datenow = date('Y-m-d', time());
    
    while ($d = $data->fetch(PDO::FETCH_ASSOC)) {
    $angka = intval($d['kd_barang']);
    
    ?>
        <tr>
            <td style='text-align:center;'><?php echo $no; ?></td>
            <td><?php echo $angka; ?></td>
            <td><?php echo $d['nm_barang']; ?></td>
            <td style='text-align:center;'>
                <?php 
                    
                    $batch  = $db->prepare("SELECT * FROM batch WHERE kd_barang = ?
                                                                            AND status = 'masuk'
                                                                            AND exp_date > ?");
                    $batch->execute([$d['kd_barang'], $datenow]);
                    // $bt     = $batch->fetch(PDO::FETCH_ASSOC);
                    // $nomorbatch = $bt['no_batch'];
                    while($bt     = $batch->fetch(PDO::FETCH_ASSOC)){
                        echo $bt['no_batch']."<br>";
                    }
                ?>
            </td>
            <td style='text-align:center;'><?php echo $d['stok_barang']; ?></td>
            <td style='text-align:center;'><?php echo $d['sat_barang']; ?></td>
            <td style='text-align:center;'><?php echo $d['jenisobat']; ?></td>
            <td><?php echo $d['hrgsat_barang']; ?></td>
            <td><?php echo $d['hrgjual_barang']; ?></td>
            <td><?php echo $d['hrgjual_barang1']; ?></td>
            <td><?php echo $d['hrgjual_barang2']; ?></td>
            <td><?php echo $d['hrgjual_barang3']; ?></td>
        </tr>
        
    <?php
    $no++;
    }
    ?>
</table>



?>