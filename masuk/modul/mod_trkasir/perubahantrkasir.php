<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_perubahan_trkasir.php";

function pt_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pt_rupiah($value)
{
    return number_format((float) $value, 0, ',', '.');
}

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
?>
<!doctype html>
<html>
<head><title>PERUBAHAN TRANSAKSI</title></head>
<body>
    <div class="error msg">Untuk mengakses halaman ini Anda harus login. <a href="../../index.php">LOGIN</a></div>
</body>
</html>
<?php
    exit;
}

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'pemilik') {
?>
<!doctype html>
<html>
<head><title>PERUBAHAN TRANSAKSI</title></head>
<body>
    <div class="alert alert-danger" style="margin:20px">Fitur ini hanya untuk pemilik.</div>
</body>
</html>
<?php
    exit;
}

pastikan_skema_perubahan_trkasir($db);

$kd_trkasir_view = isset($_GET['kd_trkasir']) ? trim($_GET['kd_trkasir']) : '';

if ($kd_trkasir_view !== '') {
    // ------------------------------------------------------------------
    // MODE DETAIL: bandingkan kondisi awal vs kondisi akhir 1 transaksi
    // ------------------------------------------------------------------
    $cekTrkasir = $db->prepare("SELECT * FROM trkasir WHERE kd_trkasir = ?");
    $cekTrkasir->execute([$kd_trkasir_view]);
    $header = $cekTrkasir->fetch(PDO::FETCH_ASSOC);

    $statusTransaksi = $header ? 'AKTIF' : 'DIHAPUS';

    if (!$header) {
        $cekRestore = $db->prepare("SELECT * FROM trkasir_restore WHERE kd_trkasir = ? ORDER BY id_butrkasir ASC LIMIT 1");
        $cekRestore->execute([$kd_trkasir_view]);
        $headerRestore = $cekRestore->fetch(PDO::FETCH_ASSOC);
    }

    // Kondisi awal: item yang tercatat sejak revisi pertama (tipetx = 1)
    $kondisiAwal = array();

    $awalLive = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ? AND tipetx = 1");
    $awalLive->execute([$kd_trkasir_view]);
    while ($row = $awalLive->fetch(PDO::FETCH_ASSOC)) {
        $kondisiAwal[$row['id_dtrkasir']] = $row;
    }

    $awalHist = $db->prepare("SELECT * FROM trkasir_detail_hist WHERE kd_trkasir = ? AND tipetx_asal = 1");
    $awalHist->execute([$kd_trkasir_view]);
    while ($row = $awalHist->fetch(PDO::FETCH_ASSOC)) {
        $kondisiAwal[$row['id_dtrkasir']] = $row;
    }

    // Kondisi akhir: kalau transaksi masih ada, live trkasir_detail. Kalau sudah dihapus, dari trkasir_restore.
    $kondisiAkhir = array();
    if ($header) {
        $akhirLive = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ?");
        $akhirLive->execute([$kd_trkasir_view]);
        while ($row = $akhirLive->fetch(PDO::FETCH_ASSOC)) {
            $kondisiAkhir[$row['id_dtrkasir']] = $row;
        }
    } else {
        $akhirRestore = $db->prepare("SELECT * FROM trkasir_restore WHERE kd_trkasir = ?");
        $akhirRestore->execute([$kd_trkasir_view]);
        while ($row = $akhirRestore->fetch(PDO::FETCH_ASSOC)) {
            $kondisiAkhir[$row['id_dtrkasir']] = $row;
        }
    }

    // Diff per item (cocokkan by id_dtrkasir untuk item yang masih ada, sisanya dianggap baru/dihapus)
    $diff = array();
    foreach ($kondisiAwal as $idDtrkasir => $rowAwal) {
        if (isset($kondisiAkhir[$idDtrkasir])) {
            $rowAkhir = $kondisiAkhir[$idDtrkasir];
            if ((float) $rowAwal['qty_dtrkasir'] != (float) $rowAkhir['qty_dtrkasir'] || (float) $rowAwal['hrgttl_dtrkasir'] != (float) $rowAkhir['hrgttl_dtrkasir']) {
                $diff[] = array('status' => 'DIUBAH', 'awal' => $rowAwal, 'akhir' => $rowAkhir);
            } else {
                $diff[] = array('status' => 'TETAP', 'awal' => $rowAwal, 'akhir' => $rowAkhir);
            }
        } else {
            $diff[] = array('status' => 'DIHAPUS', 'awal' => $rowAwal, 'akhir' => null);
        }
    }
    if ($header) {
        foreach ($kondisiAkhir as $idDtrkasir => $rowAkhir) {
            if (!isset($kondisiAwal[$idDtrkasir])) {
                $diff[] = array('status' => 'DITAMBAHKAN', 'awal' => null, 'akhir' => $rowAkhir);
            }
        }
    }

    // Timeline: gabungan ubah qty, hapus item, tambah item (hanya event setelah transaksi final)
    $timeline = array();

    $idsWithQtyLog = array();
    $logQty = $db->prepare("SELECT * FROM trkasir_detail_ubah_qty WHERE kd_trkasir = ? ORDER BY waktu ASC");
    $logQty->execute([$kd_trkasir_view]);
    while ($row = $logQty->fetch(PDO::FETCH_ASSOC)) {
        $idsWithQtyLog[] = $row['id_dtrkasir'];
        $timeline[] = array(
            'waktu' => $row['waktu'],
            'tipetx' => $row['tipetx'],
            'aksi' => 'UBAH QTY',
            'keterangan' => pt_h($row['nmbrg_dtrkasir']) . ': qty ' . pt_h($row['qty_sebelum']) . ' &rarr; ' . pt_h($row['qty_sesudah']),
            'id_admin' => $row['id_admin'],
        );
    }

    $logHapus = $db->prepare("SELECT * FROM trkasir_detail_hist WHERE kd_trkasir = ? AND tipetx_hapus > 1 ORDER BY waktu_hapus ASC");
    $logHapus->execute([$kd_trkasir_view]);
    while ($row = $logHapus->fetch(PDO::FETCH_ASSOC)) {
        $timeline[] = array(
            'waktu' => $row['waktu_hapus'],
            'tipetx' => $row['tipetx_hapus'],
            'aksi' => 'HAPUS ITEM',
            'keterangan' => pt_h($row['nmbrg_dtrkasir']) . ' (qty ' . pt_h($row['qty_dtrkasir']) . ') dihapus',
            'id_admin' => $row['id_admin_hapus'],
        );
    }

    $logTambah = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ? AND tipetx > 1");
    $logTambah->execute([$kd_trkasir_view]);
    while ($row = $logTambah->fetch(PDO::FETCH_ASSOC)) {
        // Baris yang sudah pernah tercatat di log qty dilewati di sini supaya tidak dobel --
        // riwayatnya sudah lengkap lewat entri UBAH QTY di atas.
        if (in_array($row['id_dtrkasir'], $idsWithQtyLog)) {
            continue;
        }
        $timeline[] = array(
            'waktu' => $row['waktu'],
            'tipetx' => $row['tipetx'],
            'aksi' => 'TAMBAH ITEM',
            'keterangan' => pt_h($row['nmbrg_dtrkasir']) . ' (qty ' . pt_h($row['qty_dtrkasir']) . ') ditambahkan',
            'id_admin' => $row['idadmin'],
        );
    }

    usort($timeline, function ($a, $b) {
        return strcmp($a['waktu'], $b['waktu']);
    });
}
?>
<!doctype html>
<html>
<head>
    <title>PERUBAHAN TRANSAKSI</title>
    <link rel="stylesheet" href="../../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="../../../dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="../../../plugins/datatables/dataTables.bootstrap.css">
    <script src="../../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="../../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="../../../bootstrap/js/bootstrap.min.js"></script>
</head>
<body class="hold-transition skin-blue-light sidebar-mini" style="background:#ecf0f5">
<div class="content-wrapper" style="padding:20px">

<?php if ($kd_trkasir_view === '') { ?>

    <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">PERUBAHAN TRANSAKSI</h3>
        </div>
        <div class="box-body table-responsive">
            <p>Daftar transaksi yang mengalami perubahan (item ditambah/dihapus/diubah) setelah transaksi final, atau yang dihapus total.</p>
            <table id="tabelPerubahan" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Jumlah Revisi</th>
                        <th>Total Sekarang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
<?php
    $daftar = array();

    $aktif = $db->query("SELECT kd_trkasir, tgl_trkasir, nm_pelanggan, tipetx, ttl_trkasir FROM trkasir WHERE tipetx > 1 ORDER BY tgl_trkasir DESC");
    while ($row = $aktif->fetch(PDO::FETCH_ASSOC)) {
        $daftar[] = array(
            'kd_trkasir' => $row['kd_trkasir'],
            'tgl_trkasir' => $row['tgl_trkasir'],
            'nm_pelanggan' => $row['nm_pelanggan'],
            'status' => 'AKTIF',
            'tipetx' => $row['tipetx'],
            'ttl_trkasir' => $row['ttl_trkasir'],
        );
    }

    $dihapus = $db->query("SELECT kd_trkasir, MAX(tgl_trkasir) AS tgl_trkasir, MAX(nm_pelanggan) AS nm_pelanggan, MAX(tipetx) AS tipetx, SUM(hrgttl_dtrkasir) AS ttl_trkasir FROM trkasir_restore GROUP BY kd_trkasir ORDER BY MAX(waktu_hapus) DESC");
    while ($row = $dihapus->fetch(PDO::FETCH_ASSOC)) {
        $daftar[] = array(
            'kd_trkasir' => $row['kd_trkasir'],
            'tgl_trkasir' => $row['tgl_trkasir'],
            'nm_pelanggan' => $row['nm_pelanggan'],
            'status' => 'DIHAPUS',
            'tipetx' => $row['tipetx'],
            'ttl_trkasir' => $row['ttl_trkasir'],
        );
    }

    $no = 1;
    foreach ($daftar as $d) {
?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo pt_h($d['kd_trkasir']); ?></td>
                        <td><?php echo pt_h($d['tgl_trkasir']); ?></td>
                        <td><?php echo pt_h($d['nm_pelanggan']); ?></td>
                        <td><?php echo $d['status'] === 'AKTIF' ? '<span class="label label-success">AKTIF</span>' : '<span class="label label-danger">DIHAPUS</span>'; ?></td>
                        <td><?php echo (int) $d['tipetx']; ?></td>
                        <td>Rp <?php echo pt_rupiah($d['ttl_trkasir']); ?></td>
                        <td><a class="btn btn-xs btn-info" href="?kd_trkasir=<?php echo urlencode($d['kd_trkasir']); ?>">Lihat Detail</a></td>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(function() {
            $('#tabelPerubahan').DataTable({
                order: [],
                pageLength: 25
            });
        });
    </script>

<?php } else { ?>

    <a class="btn btn-default btn-flat" href="perubahantrkasir.php">&larr; Kembali ke Daftar</a>

    <div class="box box-primary box-solid" style="margin-top:10px">
        <div class="box-header with-border">
            <h3 class="box-title">DETAIL PERUBAHAN: <?php echo pt_h($kd_trkasir_view); ?></h3>
        </div>
        <div class="box-body">
            <p>
                Status: <?php echo $statusTransaksi === 'AKTIF' ? '<span class="label label-success">AKTIF</span>' : '<span class="label label-danger">TRANSAKSI DIHAPUS</span>'; ?>
                &nbsp;
                <?php if ($header) { ?>
                    Pelanggan: <b><?php echo pt_h($header['nm_pelanggan']); ?></b> &nbsp;
                    Tanggal: <b><?php echo pt_h($header['tgl_trkasir']); ?></b> &nbsp;
                    Jumlah Revisi: <b><?php echo (int) $header['tipetx']; ?></b>
                <?php } elseif (isset($headerRestore) && $headerRestore) { ?>
                    Pelanggan: <b><?php echo pt_h($headerRestore['nm_pelanggan']); ?></b> &nbsp;
                    Tanggal: <b><?php echo pt_h($headerRestore['tgl_trkasir']); ?></b> &nbsp;
                    Jumlah Revisi sebelum dihapus: <b><?php echo (int) $headerRestore['tipetx']; ?></b>
                <?php } ?>
            </p>

            <?php
                $totalAwal = 0;
                foreach ($kondisiAwal as $rowAwal) {
                    $totalAwal += (float) $rowAwal['hrgttl_dtrkasir'];
                }
                $totalAkhir = 0;
                foreach ($kondisiAkhir as $rowAkhir) {
                    $totalAkhir += (float) $rowAkhir['hrgttl_dtrkasir'];
                }
            ?>

            <div class="row">
                <div class="col-md-6">
                    <h4>Kondisi Awal (saat transaksi pertama)</h4>
                    <table class="table table-bordered table-condensed">
                        <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
                        <tbody>
<?php foreach ($kondisiAwal as $rowAwal) { ?>
                            <tr>
                                <td><?php echo pt_h($rowAwal['nmbrg_dtrkasir']); ?></td>
                                <td><?php echo pt_h($rowAwal['qty_dtrkasir']); ?></td>
                                <td>Rp <?php echo pt_rupiah($rowAwal['hrgttl_dtrkasir']); ?></td>
                            </tr>
<?php } if (empty($kondisiAwal)) { echo '<tr><td colspan="3">Tidak ada data</td></tr>'; } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:right">Total Transaksi</th>
                                <th>Rp <?php echo pt_rupiah($totalAwal); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Kondisi Akhir (sekarang)</h4>
                    <table class="table table-bordered table-condensed">
                        <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
                        <tbody>
<?php foreach ($kondisiAkhir as $rowAkhir) { ?>
                            <tr>
                                <td><?php echo pt_h($rowAkhir['nmbrg_dtrkasir']); ?></td>
                                <td><?php echo pt_h($rowAkhir['qty_dtrkasir']); ?></td>
                                <td>Rp <?php echo pt_rupiah($rowAkhir['hrgttl_dtrkasir']); ?></td>
                            </tr>
<?php } if (empty($kondisiAkhir)) { echo '<tr><td colspan="3">Tidak ada data</td></tr>'; } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:right">Total Transaksi</th>
                                <th>Rp <?php echo pt_rupiah($totalAkhir); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <h4>Perbandingan Item</h4>
            <table class="table table-bordered table-condensed">
                <thead><tr><th>Item</th><th>Status</th><th>Qty Awal</th><th>Qty Akhir</th></tr></thead>
                <tbody>
<?php foreach ($diff as $d) {
    $nama = $d['awal'] ? $d['awal']['nmbrg_dtrkasir'] : $d['akhir']['nmbrg_dtrkasir'];
    $qtyAwal = $d['awal'] ? $d['awal']['qty_dtrkasir'] : '-';
    $qtyAkhir = $d['akhir'] ? $d['akhir']['qty_dtrkasir'] : '-';
    $labelClass = 'label-default';
    if ($d['status'] === 'DITAMBAHKAN') $labelClass = 'label-success';
    if ($d['status'] === 'DIHAPUS') $labelClass = 'label-danger';
    if ($d['status'] === 'DIUBAH') $labelClass = 'label-warning';
?>
                    <tr>
                        <td><?php echo pt_h($nama); ?></td>
                        <td><span class="label <?php echo $labelClass; ?>"><?php echo $d['status']; ?></span></td>
                        <td><?php echo pt_h($qtyAwal); ?></td>
                        <td><?php echo pt_h($qtyAkhir); ?></td>
                    </tr>
<?php } if (empty($diff)) { echo '<tr><td colspan="4">Tidak ada perbedaan item</td></tr>'; } ?>
                </tbody>
            </table>

            <h4>Riwayat Perubahan</h4>
            <table class="table table-bordered table-condensed">
                <thead><tr><th>Waktu</th><th>Revisi Ke</th><th>Aksi</th><th>Keterangan</th></tr></thead>
                <tbody>
<?php foreach ($timeline as $t) { ?>
                    <tr>
                        <td><?php echo pt_h($t['waktu']); ?></td>
                        <td><?php echo (int) $t['tipetx']; ?></td>
                        <td><?php echo pt_h($t['aksi']); ?></td>
                        <td><?php echo $t['keterangan']; ?></td>
                    </tr>
<?php } if (empty($timeline)) { echo '<tr><td colspan="4">Tidak ada riwayat</td></tr>'; } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php } ?>

</div>
</body>
</html>
