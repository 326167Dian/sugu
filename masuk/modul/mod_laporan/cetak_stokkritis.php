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
        <th>Kategori</th>
        <th>Nama Barang</th>
        <th>Qty/Stok</th>
        <th>T30</th>
        <th>Q30</th>
        <th>SFC max30</th>
        <th>SFCmax/week</th>
        <th>Satuan</th>
    </tr>
    <?php
    $hasil30 = $db->prepare("select id_barang,
                                        kd_barang,
                                        nm_barang,
                                        stok_barang,
                                        sat_barang,
                                        t30,
                                        q30
                                 from barang ");
    $hasil30->execute();
    $no = 1;
    while ($tp30 = $hasil30->fetch(PDO::FETCH_ASSOC)) {
        $qfc = $tp30['t30'] - $tp30['stok_barang'];
        $qweek = round((($tp30['q30'] - $tp30['stok_barang']) / 4), 0);

        if ($tp30['t30'] > 0 && ($tp30['stok_barang'] <= (0.25 * $tp30['t30']))) {
            if ($tp30['t30'] <= 0) {
                $kategori = 'MACET';
            } elseif ($tp30['t30'] > 0 && $tp30['t30'] <= 5) {
                $kategori = 'SLOW';
            } elseif ($tp30['t30'] > 5 && $tp30['t30'] <= 10) {
                $kategori = 'LANCAR';
            } else {
                $kategori = 'LAKU';
            }
    ?>
        <tr>
            <td style='text-align:center;'><?php echo $no; ?></td>
            <td style='text-align:center;'><?php echo $kategori; ?></td>
            <td><?php echo $tp30['nm_barang']; ?></td>
            <td style='text-align:center;'><?php echo $tp30['stok_barang']; ?></td>
            <td style='text-align:center;'><?php echo $tp30['t30']; ?></td>
            <td style='text-align:center;'><?php echo $tp30['q30']; ?></td>
            <td style='text-align:center;'><?php echo $qfc; ?></td>
            <td style='text-align:center;'><?php echo $qweek; ?></td>
            <td style='text-align:center;'><?php echo $tp30['sat_barang']; ?></td>
        </tr>
    <?php
        $no++;
        }
    }
    ?>
</table>
