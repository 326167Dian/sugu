<?php
include "../../../configurasi/koneksi.php";

$shift = $_POST['shift'];
$tgl_awal = $_POST['tgl_awal'];


?>
<table id="example10" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Nama Obat</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Rak Obat</th>
            <th class="text-center">Qty Terjual</th>
            <th class="text-center">Stok Sistem</th>
            <th class="text-center">Stok Fisik</th>
            <th class="text-center">Submit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        
        $query = $db->prepare("SELECT trkasir_detail.*, trkasir.*, barang.*,SUM(trkasir_detail.qty_dtrkasir) as ttlqty FROM trkasir_detail 
            JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
            JOIN barang ON trkasir_detail.id_barang = barang.id_barang
            WHERE trkasir.tgl_trkasir = ? AND shift = ?
            GROUP BY trkasir_detail.kd_barang");
        $query->execute([$tgl_awal, $shift]);

        $no = 1;
        while ($lihat = $query->fetch(PDO::FETCH_ASSOC)) :

            $stokopname = $db->prepare("SELECT a.*, b.* FROM barang a 
                JOIN stok_opname b ON a.kd_barang = b.kd_barang 
                WHERE a.id_barang=? AND b.shift=? AND tgl_stokopname = ?");
            $stokopname->execute([$lihat['id_barang'], $shift, $tgl_awal]);
            $stok = $stokopname->rowCount();
            $barang = $stokopname->fetch(PDO::FETCH_ASSOC);
            $terjual = $lihat['ttlqty'];

            if ($stok == 0) :

                $beli = "SELECT trbmasuk.tgl_trbmasuk,                                           
                                       SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli                                            
                                       FROM trbmasuk_detail join trbmasuk 
                                       on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                                       WHERE id_barang =?";
                $buy_stmt = $db->prepare($beli);
                $buy_stmt->execute([$lihat['id_barang']]);
                $buy2 = $buy_stmt->fetch(PDO::FETCH_ASSOC);

                $jual = "SELECT trkasir.tgl_trkasir,                                
                                        sum(trkasir_detail.qty_dtrkasir) AS totaljual
                                        FROM trkasir_detail join trkasir 
                                        on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE id_barang =?";

                $sell_stmt = $db->prepare($jual);
                $sell_stmt->execute([$lihat['id_barang']]);
                $sell = $sell_stmt->fetch(PDO::FETCH_ASSOC);
                $selisih = $buy2['totalbeli'] - $sell['totaljual'];

        ?>

                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= $lihat['kd_barang']; ?></td>
                    <td class="text-left"><?= $lihat['nm_barang']; ?></td>
                    <td class="text-center"><?= $lihat['sat_barang']; ?></td>
                    <td class="text-center"><?= $lihat['jenisobat']; ?></td>
                    <td class="text-center"><?= $terjual; ?></td>
                    <td class="text-center"><?= $selisih; ?></td>
                    <td class="text-center">
                        <input type="number" min="0" class="form-control text-center" name="stok_fisik_<?= $no ?>" id="stok_fisik_<?= $no ?>" value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" id="pilih_<?= $no ?>" class="btn btn-primary btn-sm" onclick="javascript:simpan_stok_opname('<?= $no ?>')" 
                            data-id_barang="<?= $lihat['id_barang']; ?>" 
                            data-kd_barang="<?= $lihat['kd_barang']; ?>" 
                            data-hrgsat_barang="<?= $lihat['hrgsat_barang']; ?>"
                            data-shift="<?= $lihat['shift']; ?>">
                            <i class="fa fa-fw fa-check"></i>
                            SIMPAN</button>
                    </td>
                </tr>

        <?php
            endif;
        endwhile; ?>
    </tbody>
</table>

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