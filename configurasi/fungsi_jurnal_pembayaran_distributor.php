<?php
// Menyinkronkan entri jurnal kas "Pembayaran Distributor" dengan status/nilai transaksi
// trbmasuk (mod_trbmasuk / mod_trbmasukpbf) TERBARU, dipanggil setiap kali transaksi
// disimpan (insert atau ubah), apa pun cara bayarnya saat ini.
//
// Dicari dulu apakah transaksi ini ($kd_trbmasuk) sudah pernah punya entri jurnal
// (lewat kolom jurnal.kd_referensi):
// - carabayar bukan LUNAS (atau jumlah 0)  -> hapus entri jurnal yang ada (jika ada) &
//   kembalikan saldo kas. Ini mencegah entri lama nyangkut kalau status di-downgrade
//   dari LUNAS ke KREDIT/KONSINYASI.
// - carabayar LUNAS & entri jurnal sudah ada -> update nominalnya kalau berbeda dari
//   sebelumnya (mis. karena detail item diubah), dan sesuaikan saldo kas sebesar selisihnya.
//   TIDAK membuat entri baru, supaya tidak dobel tercatat.
// - carabayar LUNAS & belum ada entri jurnal -> catat entri baru (pertama kali lunas).
if (!function_exists('sinkron_jurnal_pembayaran_distributor')) {
function sinkron_jurnal_pembayaran_distributor($db, $kd_trbmasuk, $nm_supplier, $carabayar, $jumlah, $petugas)
{
    $idjenis = 4; // fallback id jenis_jurnal 'Pembayaran Distributor'
    $cekjenis = $db->prepare("SELECT idjenis FROM jenis_jurnal WHERE nm_jurnal = ?");
    $cekjenis->execute(['Pembayaran Distributor']);
    $rjenis = $cekjenis->fetch(PDO::FETCH_ASSOC);
    if (!empty($rjenis['idjenis'])) {
        $idjenis = $rjenis['idjenis'];
    }

    $cekjurnal = $db->prepare("SELECT id_jurnal, debit FROM jurnal WHERE idjenis = ? AND kd_referensi = ?");
    $cekjurnal->execute([$idjenis, $kd_trbmasuk]);
    $jurnalada = $cekjurnal->fetch(PDO::FETCH_ASSOC);

    $jumlah = (float) $jumlah;

    if ($carabayar !== 'LUNAS' || $jumlah <= 0) {
        if ($jurnalada) {
            $db->prepare("DELETE FROM jurnal WHERE id_jurnal = ?")->execute([$jurnalada['id_jurnal']]);
            ubah_saldo_kas($db, $jurnalada['debit']); // kembalikan saldo yang tadinya dikurangi
        }
        return;
    }

    $ket = 'Pembayaran Distributor ' . $nm_supplier . ' - ' . $kd_trbmasuk;
    $curtime = date('ymdHis');

    if ($jurnalada) {
        $selisih = $jumlah - $jurnalada['debit'];
        if ($selisih != 0) {
            $db->prepare("UPDATE jurnal SET debit = ?, ket = ?, petugas = ?, current = ? WHERE id_jurnal = ?")
                ->execute([$jumlah, $ket, $petugas, $curtime, $jurnalada['id_jurnal']]);
            ubah_saldo_kas($db, -$selisih);
        }
    } else {
        $db->prepare("INSERT INTO jurnal (tanggal, ket, petugas, idjenis, kd_referensi, carabayar, debit, kredit, current) VALUES (?, ?, ?, ?, ?, 'TRANSFER', ?, 0, ?)")
            ->execute([date('Y-m-d'), $ket, $petugas, $idjenis, $kd_trbmasuk, $jumlah, $curtime]);
        ubah_saldo_kas($db, -$jumlah);
    }
}
}

if (!function_exists('ubah_saldo_kas')) {
function ubah_saldo_kas($db, $selisih)
{
    $kas = $db->prepare("SELECT saldo FROM kas WHERE id_kas = ?");
    $kas->execute(['1']);
    $rkas = $kas->fetch(PDO::FETCH_ASSOC);
    $saldobaru = $rkas['saldo'] + $selisih;
    $db->prepare("UPDATE kas SET saldo = ? WHERE id_kas = ?")->execute([$saldobaru, '1']);
}
}
