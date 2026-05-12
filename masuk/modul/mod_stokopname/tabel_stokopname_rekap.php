<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";

?>
<table id="example11" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Nama Obat</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Stok Sistem <br>(SS)</th>
            <th class="text-center">Stok Fisik <br>(SF)</th>
            <th class="text-center">Hasil <br>(SF - SS)</th>
            <th class="text-center">Current Time</th>
            <th class="text-center">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $shift = $_POST['shift'];
        $tgl = $_POST['tgl_awal'];
        // $tgl = $_POST['tgl_akhir'];

        $query1 = $db->prepare("SELECT * FROM stok_opname a 
                            JOIN barang b ON a.id_barang = b.id_barang WHERE a.shift=? AND a.tgl_stokopname = ? ORDER BY b.nm_barang");
        $query1->execute([$shift, $tgl]);

        $nomor = 1;
        while ($tampil = $query1->fetch(PDO::FETCH_ASSOC)) :

        ?>

            <tr>
                <td class="text-center"><?= $nomor++; ?></td>
                <td class="text-center"><?= $tampil['kd_barang']; ?></td>
                <td class="text-center"><?= $tampil['nm_barang']; ?></td>
                <td class="text-center"><?= $tampil['sat_barang']; ?></td>
                <td class="text-center"><?= $tampil['stok_sistem']; ?></td>
                <td class="text-center"><?= $tampil['stok_fisik']; ?></td>
                <td class="text-center"><?= $tampil['selisih']; ?></td>
                <td class="text-center"><?= date("d M Y - H:i:s", strtotime($tampil['tgl_current'])); ?></td>
                <td class="text-center">
                    <button type="button" id="hapus_<?= $tampil['id_stok_opname'] ?>" class="btn btn-danger btn-sm" onclick="javascript:hapus_stok_opname('<?= $tampil['id_stok_opname'] ?>')" data-id_stok="<?= $tampil['id_stok_opname'] ?>">
                        <i class="fa fa-fw fa-trash"></i>
                        HAPUS</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {

        $('#example11').dataTable({
            "aLengthMenu": [
                [5, 25, 50, 75, -1],
                [5, 25, 50, 75, "All"]
            ],
            "iDisplayLength": 5
        });
    });
</script>