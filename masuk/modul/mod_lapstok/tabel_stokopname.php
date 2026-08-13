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
        $query = $db->prepare("SELECT a.id_barang, a.kd_barang, a.nm_barang, a.sat_barang, a.hrgsat_barang,
                                       COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0) AS selisih
                                FROM barang a
                                LEFT JOIN (
                                    SELECT kd_barang, SUM(qty_dtrbmasuk) AS totalbeli
                                    FROM trbmasuk_detail
                                    GROUP BY kd_barang
                                ) beli ON beli.kd_barang = a.kd_barang
                                LEFT JOIN (
                                    SELECT kd_barang, SUM(qty_dtrkasir) AS totaljual
                                    FROM trkasir_detail
                                    GROUP BY kd_barang
                                ) jual ON jual.kd_barang = a.kd_barang
                                LEFT JOIN stok_opname so ON so.kd_barang = a.kd_barang AND so.tgl_stokopname = ?
                                WHERE a.jenisobat = ?
                                AND (beli.kd_barang IS NOT NULL OR jual.kd_barang IS NOT NULL)
                                AND so.id_stok_opname IS NULL
                                ORDER BY a.nm_barang");
        $query->execute([$tgl, $jenisobat]);

        $no = 1;
        while ($lihat = $query->fetch(PDO::FETCH_ASSOC)) :
            $selisih = $lihat['selisih'];
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
