<?php
// Helper laporan "Rekap Administratif Keprofesian" (menu Laporan).
// Dipakai oleh rekapkeprofesian.php (form) dan cetak_rekapkeprofesian.php (hasil cetak).

// Jaring pengaman kalau migrasi 20260924_create_table_apoteker_profesi.sql belum dijalankan.
function pastikan_tabel_apoteker_profesi($db)
{
    $db->exec("CREATE TABLE IF NOT EXISTS apoteker_profesi (
        id_admin INT(11) NOT NULL,
        nama_gelar VARCHAR(150) NOT NULL DEFAULT '',
        jabatan VARCHAR(50) NOT NULL DEFAULT 'Apoteker',
        no_stra VARCHAR(100) NOT NULL DEFAULT '',
        no_sipa VARCHAR(100) NOT NULL DEFAULT '',
        sipa_berlaku DATE NULL DEFAULT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id_admin)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
}

function rekap_keprofesian_bulan_singkat($bln)
{
    $nama = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
    return $nama[intval($bln)];
}

// Daftar bulan (format 'Y-m') dari tanggal awal s/d tanggal akhir.
function rekap_keprofesian_daftar_bulan($tglAwal, $tglAkhir)
{
    $bulan = array();
    $cur = strtotime(date('Y-m-01', strtotime($tglAwal)));
    $akhir = strtotime(date('Y-m-01', strtotime($tglAkhir)));
    while ($cur <= $akhir) {
        $bulan[] = date('Y-m', $cur);
        $cur = strtotime('+1 month', $cur);
    }
    return $bulan;
}

// Mengembalikan [bulan, grup kegiatan]. Tiap kegiatan berisi 'nama' dan 'per_bulan' (Y-m => jumlah).
function rekap_keprofesian_data($db, $tglAwal, $tglAkhir)
{
    $bulan = rekap_keprofesian_daftar_bulan($tglAwal, $tglAkhir);
    $kosong = array_fill_keys($bulan, 0);

    $ambil = function ($sql, $kolom) use ($db, $tglAwal, $tglAkhir, $kosong) {
        $hasil = array();
        foreach ($kolom as $k) {
            $hasil[$k] = $kosong;
        }
        $q = $db->prepare($sql);
        $q->execute(array($tglAwal, $tglAkhir));
        while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($kosong[$r['bln']])) {
                continue;
            }
            foreach ($kolom as $k) {
                $hasil[$k][$r['bln']] = intval($r[$k]);
            }
        }
        return $hasil;
    };

    // Surat Pesanan: orders (+ ordersdetail), jenis SP dari orders.ket_trbmasuk
    $sp = $ambil("SELECT DATE_FORMAT(o.tgl_trbmasuk, '%Y-%m') AS bln,
            SUM(CASE WHEN UPPER(TRIM(COALESCE(o.ket_trbmasuk, ''))) NOT IN ('PREKURSOR', 'OOT') THEN 1 ELSE 0 END) AS reguler,
            SUM(CASE WHEN UPPER(TRIM(o.ket_trbmasuk)) = 'PREKURSOR' THEN 1 ELSE 0 END) AS prekursor,
            SUM(CASE WHEN UPPER(TRIM(o.ket_trbmasuk)) = 'OOT' THEN 1 ELSE 0 END) AS oot
        FROM orders o
        WHERE o.tgl_trbmasuk BETWEEN ? AND ?
            AND o.kd_trbmasuk IN (SELECT kd_trbmasuk FROM ordersdetail)
        GROUP BY bln", array('reguler', 'prekursor', 'oot'));

    // Penerimaan barang dari PBF: trbmasuk (jenis pbf) + trbmasuk_detail
    $faktur = $ambil("SELECT DATE_FORMAT(t.tgl_trbmasuk, '%Y-%m') AS bln, COUNT(*) AS jumlah
        FROM trbmasuk t
        WHERE t.tgl_trbmasuk BETWEEN ? AND ?
            AND t.jenis = 'pbf'
            AND t.kd_trbmasuk IN (SELECT kd_trbmasuk FROM trbmasuk_detail)
        GROUP BY bln", array('jumlah'));

    $item = $ambil("SELECT DATE_FORMAT(t.tgl_trbmasuk, '%Y-%m') AS bln, COUNT(d.id_dtrbmasuk) AS jumlah
        FROM trbmasuk_detail d
        JOIN trbmasuk t ON t.kd_trbmasuk = d.kd_trbmasuk
        WHERE t.tgl_trbmasuk BETWEEN ? AND ?
            AND t.jenis = 'pbf'
        GROUP BY bln", array('jumlah'));

    // Pengeluaran / penyerahan: trkasir yang punya detail
    $keluar = $ambil("SELECT DATE_FORMAT(t.tgl_trkasir, '%Y-%m') AS bln, COUNT(*) AS jumlah
        FROM trkasir t
        WHERE t.tgl_trkasir BETWEEN ? AND ?
            AND t.kd_trkasir IN (SELECT kd_trkasir FROM trkasir_detail)
        GROUP BY bln", array('jumlah'));

    // Pelaporan SIPNAP dianggap 1 laporan per bulan
    $sipnap = array_fill_keys($bulan, 1);

    $grup = array(
        'A. Perencanaan dan Pengadaan' => array(
            array('nama' => 'Membuat Surat Pesanan (SP) obat reguler dan alat kesehatan ke PBF', 'per_bulan' => $sp['reguler']),
            array('nama' => 'Membuat Surat Pesanan (SP) obat mengandung Prekursor Farmasi', 'per_bulan' => $sp['prekursor']),
            array('nama' => 'Membuat Surat Pesanan (SP) Obat-Obat Tertentu (OOT)', 'per_bulan' => $sp['oot']),
        ),
        'B. Penerimaan dan Penyimpanan' => array(
            array('nama' => 'Penerimaan barang dari PBF dan pemeriksaan kesesuaian faktur dengan SP (jumlah faktur)', 'per_bulan' => $faktur['jumlah']),
            array('nama' => 'Pencatatan nomor batch dan tanggal kedaluwarsa item yang diterima (jumlah item)', 'per_bulan' => $item['jumlah']),
        ),
        'C. Pengeluaran / Penyerahan' => array(
            array('nama' => 'Pencatatan pengeluaran/penyerahan sediaan farmasi, alkes dan BMHP (jumlah transaksi)', 'per_bulan' => $keluar['jumlah']),
        ),
        'D. Pelaporan Eksternal' => array(
            array('nama' => 'Pelaporan penggunaan Narkotika melalui SIPNAP (jumlah laporan)', 'per_bulan' => $sipnap),
            array('nama' => 'Pelaporan penggunaan Psikotropika melalui SIPNAP (jumlah laporan)', 'per_bulan' => $sipnap),
        ),
    );

    return array($bulan, $grup);
}
