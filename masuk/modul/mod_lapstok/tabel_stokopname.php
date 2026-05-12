<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {
include "../../../configurasi/koneksi.php";

$jenisobat = $_POST['jenisobat'];
$tgl = $_POST['tgl_awal'];

?>
<table id="example10" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Nama Obat</th>
            <th class="text-center">Satuan</th>

            <?php
                $lupa = $_SESSION['level'];
                if ($lupa == 'pemilik') {
                echo "<th class='text-center'>Stok Sistem</th>";
                }
             ?>

            <th class="text-center">Stok Fisik</th>
            <th class="text-center">Exp Date</th>
            <th class="text-center">jumlah</th>
            <th class="text-center">Submit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // $query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang a WHERE a.jenisobat='$jenisobat' AND a.id_barang NOT IN (SELECT id_barang as idb FROM stok_opname b WHERE b.id_barang = a.id_barang AND b.tgl_current = '$time') ORDER BY a.nm_barang");

        $query = $db->prepare("SELECT * FROM barang a 
                                WHERE a.jenisobat = ? and stok_barang>0 
                                ORDER BY a.nm_barang");
        $query->execute([$jenisobat]);

        $no = 1;
        while ($lihat = $query->fetch(PDO::FETCH_ASSOC)) :

            $stokopname = $db->prepare("SELECT * FROM stok_opname a 
                                        WHERE a.id_barang = ? 
                                        AND a.tgl_stokopname = ?");
            $stokopname->execute([$lihat['id_barang'], $tgl]);
            $stok = $stokopname->rowCount();

            if ($stok == 0) :

                $beli = "SELECT trbmasuk.tgl_trbmasuk,                                           
                                       SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli                                            
                                       FROM trbmasuk_detail join trbmasuk 
                                       on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                                       WHERE id_barang =?";
                $buy = $db->prepare($beli);
                $buy->execute([$lihat['id_barang']]);
                $buy2 = $buy->fetch(PDO::FETCH_ASSOC);

                $jual = "SELECT trkasir.tgl_trkasir,                                
                                        sum(trkasir_detail.qty_dtrkasir) AS totaljual
                                        FROM trkasir_detail join trkasir 
                                        on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE id_barang =?";

                $jokul = $db->prepare($jual);
                $jokul->execute([$lihat['id_barang']]);
                $sell = $jokul->fetch(PDO::FETCH_ASSOC);
                $selisih = $buy2['totalbeli'] - $sell['totaljual'];

        ?>

                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= $lihat['kd_barang']; ?></td>
                    <td class="text-left"><?= $lihat['nm_barang']; ?></td>
                    <td class="text-center"><?= $lihat['sat_barang']; ?></td>

                    <?php
                    $lupa = $_SESSION['level'];
                    if ($lupa == 'pemilik') {
                        echo "<td class='text-center'> $selisih </td>";
                    }
                    ?>


                    <td class="text-center">
                        <input type="number" min="0" class="form-control text-center" name="stok_fisik_<?= $no ?>" id="stok_fisik_<?= $no ?>" value="0">
                    </td>
                    <td class="text-center">
                        <input type="date" class="form-control text-center" name="exp_date_<?= $no ?>" id="exp_date_<?= $no ?>" >
                    </td>
                    <td class="text-center">
                        <input type="number" min="0" class="form-control text-center" name="jml_<?= $no ?>" id="jml_<?= $no ?>" value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" id="pilih_<?= $no ?>" class="btn btn-primary btn-sm" onclick="javascript:simpan_stok_opname('<?= $no ?>')" data-id_barang="<?= $lihat['id_barang']; ?>" data-kd_barang="<?= $lihat['kd_barang']; ?>" data-hrgsat_barang="<?= $lihat['hrgsat_barang']; ?>">
                            <i class="fa fa-fw fa-check"></i>
                            SIMPAN</button>
                    </td>
                </tr>

        <?php
            endif;
        endwhile; ?>
    </tbody>
</table>
<?php
}
?>
<script>
    $(document).ready(function() {
        $('#example10').dataTable({
            "aLengthMenu": [
                [5, 25, 50, 75, -1],
                [5, 25, 50, 75, "All"]
            ],
            "iDisplayLength": 5
        });

    })
</script>
