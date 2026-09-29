<?php
// Mencatat pembayaran distributor ke jurnal kas saat status trbmasuk (mod_trbmasuk / mod_trbmasukpbf)
// berubah menjadi LUNAS, baik lewat insert transaksi baru maupun ubah status.
function catat_jurnal_pembayaran_distributor($db, $kd_trbmasuk, $nm_supplier, $jumlah, $petugas)
{
    $jumlah = (float) $jumlah;
    if ($jumlah <= 0) {
        return;
    }

    $idjenis = 4; // fallback id jenis_jurnal 'Pembayaran Distributor'
    $cekjenis = $db->prepare("SELECT idjenis FROM jenis_jurnal WHERE nm_jurnal = ?");
    $cekjenis->execute(['Pembayaran Distributor']);
    $rjenis = $cekjenis->fetch(PDO::FETCH_ASSOC);
    if (!empty($rjenis['idjenis'])) {
        $idjenis = $rjenis['idjenis'];
    }

    $ket = 'Pembayaran Distributor ' . $nm_supplier . ' - ' . $kd_trbmasuk;
    $curtime = date('ymdHis');

    $db->prepare("INSERT INTO jurnal (tanggal, ket, petugas, idjenis, carabayar, debit, kredit, current) VALUES (?, ?, ?, ?, 'TRANSFER', ?, 0, ?)")
        ->execute([date('Y-m-d'), $ket, $petugas, $idjenis, $jumlah, $curtime]);

    $kas = $db->prepare("SELECT saldo FROM kas WHERE id_kas = ?");
    $kas->execute(['1']);
    $rkas = $kas->fetch(PDO::FETCH_ASSOC);
    $saldobaru = $rkas['saldo'] - $jumlah;
    $db->prepare("UPDATE kas SET saldo = ? WHERE id_kas = ?")->execute([$saldobaru, '1']);
}
